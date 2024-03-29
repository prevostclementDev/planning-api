<?php

namespace App\Entities;

use App\Models\SchoolSpacesModel;
use App\Models\UserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Entity\Entity;

class Schoolspace extends Entity
{
    protected $datamap = [
        'name' => null,
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    public function deleteHim(): bool
    {

        if ( isset($this->attributes['id']) ) {

            $userModel = new UserModel();
            $schoolModel = new SchoolSpacesModel();

            try {
                $userModel->where('id_school_space',$this->attributes['id'])->delete();
                $schoolModel->where('id',$this->attributes['id'])->delete();
            } catch (DatabaseException $e) {
                return false;
            }

            return true;

        }

        return false;

    }

}
