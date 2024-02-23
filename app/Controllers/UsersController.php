<?php

namespace App\Controllers;

use App\Libraries\ResponseFormat;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class UsersController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;


    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat();
    }

    public function create(): ResponseInterface
    {

        $dataUser = $this->request->getPost(['first_name', 'last_name', 'mail', 'password', 'profile_picture','roles']);

        $dataUser['id_school_space'] = CURRENT_USER->id_school_space;

        if (! in_array($dataUser['roles'],[1,2,3])) {
            return $this->respond(
                $this->responseFormat->setError(400,'Format du roles invalide (1 = administrateur ,2 = eleve,3 = intervenant)')->getResponse(),
                400
            );
        }

        $insertion = insertNewData('UserModel', $dataUser);

        return $this->respond(
            $insertion['response']->getResponse(),
            $insertion['response']->getCode(),
        );

    }

    public function get() : ResponseInterface {

        $params = $this->request->getGet(['page','search']);

        $userModel = $this->queryOnSchoolspace();

        if ( ! is_null($params['search']) ) {
            $userModel->searchUser($params['search']);
        }

        $data = $userModel->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'users')->addData(createPager($userModel),'pagination')->getResponse(),
            200
        );

    }

    public function getOne(int $id) : ResponseInterface {


        if ( CURRENT_USER->id === $id || CURRENT_USER->canDo(['see_all_users']) ) {

            $userModel = new UserModel();
            $user = $userModel->find($id);

            if ( is_null($user) ) {
                $this->responseFormat->setError(404);
            }

            return $this->respond(
                $this->responseFormat->addData($user)->getResponse(),
                $this->responseFormat->getCode()
            );

        }

        return $this->respond(
            $this->responseFormat->setError(403),
            403
        );

    }

    private function queryOnSchoolspace(): UserModel
    {
        $userModel = new UserModel();
        $userModel->where('id_school_space', CURRENT_USER->id_school_space);
        return $userModel;
    }

}

