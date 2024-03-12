<?php

namespace App\Entities;

class Slot extends \CodeIgniter\Entity
{

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

}