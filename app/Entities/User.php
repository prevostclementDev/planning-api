<?php

namespace App\Entities;

use App\Models\RolesModel;
use App\Models\SchoolSpacesModel;
use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $datamap = [
        'id' => null,
        'first_name' => null,
        'last_name' => null,
        'mail' => null,
        'profile_picture' => null,
        'roles' => null,
        'password' => null,
        'id_school_space' => null,
        'last_connexion' => null
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    public function getRoles(): object|null
    {
        $roleModel = new RolesModel();
        return $roleModel->find($this->attributes['roles']);
    }

    public function canDo(array $permissions): bool
    {

        $currentUserPermissions = $this->getRoles()->getPermissions();

        $nbPermissions = count($permissions);

        foreach ( $currentUserPermissions as $permission ) {
            if ( in_array($permission['permission'], $permissions) ) $nbPermissions--;
        }

        if ( $nbPermissions === 0 ) return true;

        return false;

    }

    public function getLinkSchoolSpaces() : object|null
    {
        $schoolspaceModel = new SchoolSpacesModel();
        return $schoolspaceModel->find($this->attributes['id_school_space']);
    }

}
