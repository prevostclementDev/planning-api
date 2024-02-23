<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Role extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    public function getPermissions() : array {

        $db      = \Config\Database::connect();
        $query =
            $db->table('pm_permissions_roles')
                ->select('pm_permissions.permission')
                ->join( 'pm_roles', 'pm_roles.id = pm_permissions_roles.id_role')
                ->join( 'pm_permissions', 'pm_permissions.id = pm_permissions_roles.id_permission')
                ->where('pm_roles.id',$this->attributes['id'])
                ->get();

        return $query->getResultArray();

    }

}
