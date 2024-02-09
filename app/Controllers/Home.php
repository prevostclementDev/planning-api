<?php

namespace App\Controllers;

use App\Models\TestModel;

class Home extends BaseController
{
    public function index(): string
    {

        $testModel = new TestModel();

        var_dump( $testModel->findAll() );

        return view('welcome_message');
    }
}
