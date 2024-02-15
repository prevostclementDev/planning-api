<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class Home extends BaseController
{
    #[NoReturn] public function index () {

        echo 'test controller';
        die();

    }
}