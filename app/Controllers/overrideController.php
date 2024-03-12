<?php

namespace App\Controllers;

use App\Libraries\ResponseFormat;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

// Override error
class overrideController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;

    public function __construct()
    {
        $this->responseFormat = new ResponseFormat();
    }

    // return 404 global format
    public function override404(): ResponseInterface
    {

        if ( ! responseFormat::$headerStackAsSet ) {
            responseFormat::setAllDefaultHeader();
        }

        return $this->respond(
            $this->responseFormat
                ->setError(404,'La ressource est introuvable.')
                ->getResponse(),
            404
        );
    }

    public function overrideOption() : ResponseInterface {

        if ( ! responseFormat::$headerStackAsSet ) {
            responseFormat::setAllDefaultHeader();
        }

        return $this->respond( $this->responseFormat->getResponse() );
    }

}