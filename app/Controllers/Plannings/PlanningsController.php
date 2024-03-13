<?php

namespace App\Controllers\Plannings;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\ProgramsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class PlanningsController extends BaseController
{

    use ResponseTrait;
    public ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('Planning inexistant.');
    }

    // create planning
    public function create(): ResponseInterface
    {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
            'id_class',
            'id_programs',
            'name'
        ]);

        $data['status'] = 'draft';
        $data['id_school_space'] = CURRENT_USER->id_school_space;

        // If isset check if id_class exist
        // Else ci4 validation have to make the jobs
        if ( isset($data['id_class']) ) {

            $classModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');
            $class = $classModel->find($data['id_class']);

            if ( is_null($class) ) {
                return $this->respond(
                    $this->responseFormat->setError(400,'La classe choisi n\'existe pas')->getResponse(),
                    400
                );
            }

        }

        // If isset check if id_programs exist
        // Else ci4 validation have to make the jobs
        if ( isset($data['id_programs']) ) {

            $programModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');
            $program = $programModel->find($data['id_programs']);

            if ( is_null($program) ) {
                return $this->respond(
                    $this->responseFormat->setError(400,'Le programme choisi n\'existe pas')->getResponse(),
                    400
                );
            }

        }

        $save = insertNewData('PlanningModel',$data);

        return $this->respond(
            $save['response']->getResponse(),
            $save['response']->getCode()
        );

    }

    // delete planning
    public function delete(int $id): ResponseInterface {

        $PlanningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');
        $PlanningSlotModel = CURRENT_USER->queryOnSchoolSpace('PlanningsSlotModel');
        $planning = $PlanningModel->find($id);

        if ( is_null($planning) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        try {
            $PlanningModel->delete($planning);
            $PlanningSlotModel->where('id_planning',$id)->delete();
        } catch (DatabaseException $databaseException) {
            $this->responseFormat->setError();
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // update planning
    public function update(int $id): ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
            'id_class',
            'id_programs',
            'name',
            'status'
        ]);

        $PlanningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');
        $planning = $PlanningModel->find($id);

        // If planning not exist
        if ( is_null($planning) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        // If isset check if id_class exist
        // Else ci4 validation have to make the jobs
        if ( isset($data['id_class']) ) {

            $data['id_class'] = strval($data['id_class']);

            $classModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');
            $class = $classModel->find($data['id_class']);

            if ( is_null($class) ) {
                return $this->respond(
                    $this->responseFormat->setError(400,'La classe choisi n\'existe pas')->getResponse(),
                    400
                );
            }

        }

        // If isset check if id_programs exist
        // Else ci4 validation have to make the jobs
        if ( isset($data['id_programs']) ) {

            $data['id_programs'] = strval($data['id_programs']);

            $programModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');
            $program = $programModel->find($data['id_programs']);

            if ( is_null($program) ) {
                return $this->respond(
                    $this->responseFormat->setError(400,'Le programme choisi n\'existe pas')->getResponse(),
                    400
                );
            }

        }

        $planning->fill($data);

        $this->responseFormat = updateData($planning, 'PlanningModel');

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // get all planning
    public function get(): ResponseInterface
    {

        $params = $this->request->getGet(['page', 'search', 'programs', 'class', 'status']);

        $planningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');

        $permissionsToShow = [];

        // get all permissions to show planning
        if (CURRENT_USER->canDo(['show_planning_draft'])) $permissionsToShow[] = 'draft';
        if (CURRENT_USER->canDo(['show_planning_in_validation'])) $permissionsToShow[] = 'in_validation';
        if (CURRENT_USER->canDo(['show_planning_publish'])) $permissionsToShow[] = 'publish';

        // Paramètre
        if (isset($params['search']) && !empty($params['search'])) {
            $planningModel->like('name', $params['search']);
        }

        if (isset($params['programs']) && !is_integer($params['programs'])) {
            $planningModel->where('id_programs', $params['programs']);
        }

        if (isset($params['class']) && !is_integer($params['class'])) {
            $planningModel->where('id_class', $params['class']);
        }

        if ( isset($params['status']) && ! in_array($params['status'],$permissionsToShow) ) {
            $planningModel->where('status',$params['status']);

        } else {

            // where clause for planning
            $planningModel->groupStart();
            foreach ($permissionsToShow as $statusEnable) {
                $planningModel->orWhere('status',$statusEnable);
            }
            $planningModel->groupEnd();

        }

        $data = $planningModel->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'plannings')->addData(createPager($planningModel),'pagination')->getResponse(),
            200
        );

    }

    // get one planning
    public function getOne(int $id): ResponseInterface {

        $planningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');
        $planning = $planningModel->find($id);

        $params = $this->request->getGet(['start_date']);

        if ( is_null($planning) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        if ( CURRENT_USER->canDo([ 'show_planning_'.$planning->status ]) ) {

            $start_date = (isset($params['start_date'])) ? $params['start_date'] : date('Y-d-m') ;

            $dateWithFiveDay = new \DateTime($start_date);
            $dateWithFiveDay->modify('+5 days');

            $slotsPlanning = $planning->getSlot(
                $start_date,
                $dateWithFiveDay->format('Y-d-m'),
                CURRENT_USER
            );

            unset($planning->id_school_space);

            $programsModel = new ProgramsModel();
            $program = $programsModel->find($planning->id_programs);

            unset($program->id_school_space);

            return $this->respond(
                $this->responseFormat
                    ->addData($planning,'planning')
                    ->addData($program,'program')
                    ->addData($slotsPlanning,'slots')
                    ->getResponse()
            );
        }

        return $this->respond(
            $this->responseFormat->setError(403,'Vous ne pouvez pas voir ce planning')->getResponse(),
            403
        );

    }

    // get programs with completed hours
    public function getPrograms(int $id) : ResponseInterface {

        $planningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');
        $planning = $planningModel->find($id);

        if ( is_null($planning) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $this->responseFormat = $planning->getPrograms();

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

}