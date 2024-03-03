<?php

namespace App\Controllers\Class;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\ClassUserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class ClassController extends BaseController
{

    use ResponseTrait;

    public ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('La classe n\'existe pas.');
    }

    // create class
    public function create(): ResponseInterface {

        $post = get_object_vars($this->request->getJSON());
        $data = allowDataPicker($post,['name']);

        $data['id_school_space'] = CURRENT_USER->id_school_space;

        $save = insertNewData('ClassModel',$data);

        return $this->respond(
            $save['response']->getResponse(),
            $save['response']->getCode(),
        );

    }

    // update class
    public function update(int $id): ResponseInterface {

        $classModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');
        $class = $classModel->find($id);

        if ( is_null($class) ) {
            return $this->respond(
                $this->responseFormat->setError(404),
                404
            );
        }

        $post = get_object_vars($this->request->getJSON());
        $data = allowDataPicker($post,['name']);

        if ( empty($data) ) {
            return $this->respond(
                $this->responseFormat->setError(400,'Les données ne sont pas valide')->getResponse(),
                400
            );
        }

        $class->fill($data);

        $this->responseFormat = updateData($class,'ClassModel');

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // get all class
    public function get(): ResponseInterface {

        $params = $this->request->getGet(['page','search']);

        $classModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');

        if ( isset($params['search']) && ! empty($params['search']) ) {
            $classModel->like('name',$params['search']);
        }

        $data = $classModel->select('name,id')->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'programs')->addData(createPager($classModel),'pagination')->getResponse(),
            200
        );

    }

    // get one class
    public function getOne(int $id): ResponseInterface {

        $ClassModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');
        $class = $ClassModel->select('name,id')->find($id);

        if ( is_null($class) ) {
            $this->responseFormat->setError(404);
        } else {
            $this->responseFormat->addData($class);
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // delete class
    public function delete(int $id): ResponseInterface
    {

        $ClassModel = CURRENT_USER->queryOnSchoolSpace('ClassModel');
        $ClassUserModel = new ClassUserModel();
        $class = $ClassModel->find($id);

        if ( is_null($class) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        try {
            $ClassModel->delete($id);
            $ClassUserModel->where('id_class',$id)->delete();
        } catch (DatabaseException $databaseException) {
            $this->responseFormat->setError();
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }


}