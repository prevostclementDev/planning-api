<?php

namespace App\Controllers\Plannings;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use CodeIgniter\API\ResponseTrait;
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
    public function delete(int $id){



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
    public function get(){}

    // get one planning
    public function getOne(int $id){}

}