<?php

namespace App\Controllers;

use App\Libraries\Authentification;
use App\Libraries\ResponseFormat;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{

    use ResponseTrait;

    private Authentification $authentification;
    private ResponseFormat $responseFormat;

    public function __construct() {
        helper('cookie');

        $this->authentification = new Authentification();
        $this->responseFormat = new ResponseFormat();
    }

    public function connection(): ResponseInterface
    {

        // only post request
        if ( ! $this->request->is('post') ) {
            return $this->respond(
                $this->responseFormat->setError(405)->getResponse(),
                405
            );
        }

        // load valitation services
        $validation = \Config\Services::validation();

        // get post data by services validation
        $data = $this->request->getPost( array_keys($validation->getRuleGroup('usersAuth')) );

        // run validation
        if ( ! $validation->run($data, 'usersAuth') ) {
            return $this->respond([
                $this->responseFormat->setError(400)->addData($validation->getErrors())->getResponse(),
            ],400);
        }

        // création du token ( et authentification )
        $token = $this->authentification->generateToken( $data['mail'], $data['password'] );
        if ( ! $token['status'] ) {
            return $this->respond([
                $this->responseFormat->setError(403)->addData($token['message'])->getResponse(),
            ],403);
        }

        // set cookie in response
        $this->response->setCookie($token['cookie']);

        // finale response with csrf
        return $this->respond(
            $this->responseFormat->addData([
                'csrf' => $token['csrf'],
            ])->getResponse(),
            200
        );

    }

}
