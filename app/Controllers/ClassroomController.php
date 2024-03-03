<?php

namespace App\Controllers;

use App\Libraries\ResponseFormat;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class ClassroomController extends BaseController
{

    use ResponseTrait;
    public ResponseFormat $responseFormat;

    // init
    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('Salle de classe non trouvée');
    }

    // create classroom
    public function create() : ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
            'name',
            'capacity'
        ]);

        $data['id_school_space'] = CURRENT_USER->id_school_space;

        $save = insertNewData('ClassroomModel',$data);

        return $this->respond(
            $save['response']->getResponse(),
            $save['response']->getCode(),
        );

    }

    // get classroom
    public function get(): ResponseInterface {

        $params = $this->request->getGet(['page','search']);

        $ClassroomModel = CURRENT_USER->queryOnSchoolSpace('ClassroomModel');

        if ( isset($params['search']) && ! empty($params['search']) ) {
            $ClassroomModel->like('name',$params['search']);
        }

        $data = $ClassroomModel->select('name,id,capacity')->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat
                ->addData($data,'classrooms')
                ->addData(createPager($ClassroomModel),'pagination')
                ->getResponse(),
            200
        );

    }

    // delete classroom
    public function delete(int $id): ResponseInterface {

        $ClassroomModel = CURRENT_USER->queryOnSchoolSpace('ClassroomModel');
        $classroom = $ClassroomModel->find($id);

        if ( is_null($classroom) ) {
            $this->responseFormat->setError(404);
        } else {

            try {
                $ClassroomModel->delete($id);
            } catch (DatabaseException $databaseException) {
                $this->responseFormat->setError();
            }

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // update classroom
    public function update(int $id): ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post,[
           'name',
           'capacity'
        ]);

        if ( empty($data) ) {
            return $this->respond(
                $this->responseFormat->setError(400,'Donnée invalide')->getResponse(),
                400
            );
        }

        $ClassroomModel = CURRENT_USER->queryOnSchoolSpace('ClassroomModel');
        $classroom = $ClassroomModel->find($id);

        if ( is_null($classroom) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $classroom->fill($data);

        $this->responseFormat = updateData($classroom,'ClassroomModel');

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );


    }

}
