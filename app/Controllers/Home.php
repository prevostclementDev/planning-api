<?php

namespace App\Controllers;

use App\Models\UserModelisation;

class Home extends BaseController
{
    public function index(): void {

        var_dump($_ENV['SECRET_KEY']);

        $user = new UserModelisation();

        var_dump($user->findAll());

        dd('test 2');

    }
}