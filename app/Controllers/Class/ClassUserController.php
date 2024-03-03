<?php

namespace App\Controllers\Class;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use CodeIgniter\Api\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class ClassUserController extends BaseController
{

    use ResponseTrait;

    public ResponseFormat $responseFormat;

    // init
    public function __construct() {
        helper('database');
        $this->responseFormat = new ResponseFormat('La classe n\'existe pas.');
    }

    // add user to class
    public function add(int $idClass,int $idUser): ResponseInterface {

        $class = CURRENT_USER
            ->queryOnSchoolSpace('ClassModel')
            ->find($idClass);

        if ( is_null($class) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $this->responseFormat = $class->addStudent($idUser,CURRENT_USER);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // get user from class
    public function get(int $idClass): ResponseInterface
    {

        $class = CURRENT_USER->queryOnSchoolSpace('ClassModel')->find($idClass);
        $params = $this->request->getGet(['page']);

        if ( is_null($class) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $courses = $class->getStudents(25,$params);

        $this->responseFormat
            ->addData($courses['result'],'courses')
            ->addData(createPager($courses['model']),'pagination');

        return $this->respond(
            $this->responseFormat->getResponse(),
            200
        );

    }

    // delete user from class
    public function delete(int $idClass,int $idUser): ResponseInterface
    {

        $class = CURRENT_USER->queryOnSchoolSpace('ClassModel')->find($idClass);

        if ( is_null($class) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $this->responseFormat = $class->deleteStudent($idUser);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

}