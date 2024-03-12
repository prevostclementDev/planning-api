<?php

namespace App\Controllers;

use App\Libraries\Authentification;
use App\Libraries\ResponseFormat;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

// Global authentication of user
class AuthController extends BaseController
{

    use ResponseTrait;

    private Authentification $authentification;
    private ResponseFormat $responseFormat;

    // init
    public function __construct() {
        helper('cookie');
        helper('database');

        $this->authentification = new Authentification();
        $this->responseFormat = new ResponseFormat();
    }

    // AUTH USER
    // RETURN : CSRF and JWT
    public function connection(): ResponseInterface
    {

        // load valitation services
        $validation = \Config\Services::validation();

        // get post data by services validation
        $post = get_object_vars($this->request->getJSON());
        $data = allowDataPicker( $post , array_keys($validation->getRuleGroup('usersAuth')) );

        // run validation
        if ( ! $validation->run($data, 'usersAuth') ) {
            return $this->respond(
                $this->responseFormat->setError(400)->addData($validation->getErrors())->getResponse()
            ,400);
        }

        // create token
        $token = $this->authentification->generateToken( $data['mail'], $data['password'] );

        if ( ! $token['status'] ) {
            return $this->respond(
                $this->responseFormat->setError(403)->addData($token['message'])->getResponse()
            ,403);
        }

        // set cookie in response
        $this->response->setCookie($token['cookie']);

        // render response with csrf
        return $this->respond(
            $this->responseFormat
                ->addData($token['csrf'],'csrf')
                ->addData($token['user'],'user')
                ->addData($token['user']->getRoles()->getPermissions(),'permissions')
                ->getResponse(),
            200
        );

    }

}
