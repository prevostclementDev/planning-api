<?php

namespace App\Controllers\Plannings;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\PlanningsSlotsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class PlanningsSlotsController extends BaseController
{

    use ResponseTrait;
    public ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('Planning inexistant.');
    }

    // create slot for planning
    public function create(int $idPlanning): ResponseInterface
    {

        $planning = $this->planningExist($idPlanning);

        if ( is_bool($planning) && ! $planning ) {
            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode(),
            );
        }

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
           'name',
           'start_hour',
           'end_hour',
           'daydate',
           'id_course',
           'id_teacher',
           'id_classrom',
        ]);

        $data['teacher_status'] = 'waiting';

        // load valitation services
        $validation = \Config\Services::validation();

        // run validation
        if ( ! $validation->run($data, 'planningSlots') ) {
            return $this->respond([
                $this->responseFormat->setError(400)->addData($validation->getErrors())->getResponse(),
            ],400);
        }

        $this->responseFormat = $planning->addSlot($data,CURRENT_USER);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // get slot of planning
    public function get(int $idPlanning): ResponseInterface {

        $planning = $this->planningExist($idPlanning);

        $params = $this->request->getGet(['start_date','end_date']);

        if ( is_bool($planning) && ! $planning ) {
            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode(),
            );
        }

        if ( ! CURRENT_USER->canDo([ 'show_planning_'.$planning->status ]) ) {
            return $this->respond(
                $this->responseFormat->setError(403,'Vous ne pouvez pas accéder aux ressources ce planning')->getResponse(),
                403
            );
        }

        $start_date = (isset($params['start_date'])) ? $params['start_date'] : date('Y-d-m') ;

        $start_date_format = new \DateTime($start_date);
        $start_date_format->modify('+5 days');

        $end_date = (isset($params['end_date'])) ? $params['end_date'] : $start_date_format->format('Y-d-m') ;

        $slot = $planning->getSlot($start_date,$end_date, CURRENT_USER);

        return $this->respond(
            $this->responseFormat->addData($slot,'slots')->getResponse(),
            200
        );

    }

    // update slot of planning
    public function update(int $idPlanning, int $id): ResponseInterface
    {

        $planning = $this->planningExist($idPlanning);

        if ( is_bool($planning) && ! $planning ) {
            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode(),
            );
        }

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
            'name',
            'start_hour',
            'end_hour',
            'daydate',
            'id_course',
            'id_teacher',
            'id_classrom',
            'teacher_status'
        ]);

        $this->responseFormat = $planning->updateSlot($id, $data ,CURRENT_USER);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // delete slot for planning
    public function delete(int $idPlanning, int $id): ResponseInterface {

        $planning = $this->planningExist($idPlanning);

        if ( is_bool($planning) && ! $planning ) {
            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode(),
            );
        }

        $this->responseFormat = $planning->removeSlot($id);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // check if planning exist
    private function planningExist(int $idPlanning){
        $planningModel = CURRENT_USER->queryOnSchoolSpace('PlanningModel');
        $planning = $planningModel->find($idPlanning);

        if ( is_null($planning) ) {
            $this->responseFormat->setError(404);
            return false;
        }

        return $planning;
    }

}