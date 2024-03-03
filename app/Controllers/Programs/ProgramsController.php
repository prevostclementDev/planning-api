<?php

namespace App\Controllers\Programs;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\ProgramsCoursesModel;
use App\Models\ProgramsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

// Gestion des programmes scolaire (Ajout de cours dedans)
class ProgramsController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;
    private ProgramsModel $programsModel;

    // init
    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('Le programme n\'existe pas');
    }

    // Create programs
    public function create(): ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker( $post, ['name'] );

        $data['id_school_space'] = CURRENT_USER->id_school_space;

        $savePrograms = insertNewData('ProgramsModel',$data);

        return $this->respond(
            $savePrograms['response']->getResponse(),
            $savePrograms['response']->getCode()
        );

    }

    // get programs with filter
    public function get(): ResponseInterface
    {

        $params = $this->request->getGet(['page','search']);

        $programsModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');

        if ( isset($params['search']) && ! empty($params['search']) ) {
            $programsModel->like('name',$params['search']);
        }

        $data = $programsModel->select('name,id')->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'programs')->addData(createPager($programsModel),'pagination')->getResponse(),
            200
        );

    }

    // get program by ID
    public function getOne(int $idProgram): ResponseInterface
    {

        $programsModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');
        $program = $programsModel->select('name,id')->find($idProgram);

        if ( is_null($program) ) {
            $this->responseFormat->setError(404);
        } else {
            $this->responseFormat->addData($program);
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // update program name
    public function update(int $idProgram): ResponseInterface
    {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post , [ 'name' ]);

        if ( empty( $data ) ) {
            return $this->respond(
                $this->responseFormat->setError(400,'Donnée(s) non valide')->getResponse(),
                400
            );
        }

        $programsModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');
        $program = $programsModel->find($idProgram);

        if ( is_null($program) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $program->fill($data);

        $this->responseFormat = updateData($program,'ProgramsModel');

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // delete program by id
    public function delete(int $idProgram): ResponseInterface {

        $programsModel = CURRENT_USER->queryOnSchoolSpace('ProgramsModel');
        $programCourseModel = new ProgramsCoursesModel();
        $program = $programsModel->find($idProgram);

        if ( is_null($program) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        try {
            $programsModel->delete($idProgram);
            $programCourseModel->where('id_programs',$idProgram)->delete();
        } catch (DatabaseException $databaseException) {
            $this->responseFormat->setError();
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

}
