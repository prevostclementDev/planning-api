<?php

namespace App\Filters;

use App\Libraries\ResponseFormat;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;


class CORS implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {

        if ( ! ResponseFormat::$headerStackAsSet ) {

            ResponseFormat::setAllDefaultHeader();

        }

    }

}