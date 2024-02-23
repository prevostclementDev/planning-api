<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermissionsManageSchool extends Migration
{

    public function up()
    {
        $this->db->table('pm_permissions')->insert([
            'permission' => 'manage_schoolspace'
        ]);

        $permissionId = $this->db->insertID();

        $this->db->table('pm_permissions_roles')->insert([
            'id_permission' => $permissionId,
            'id_role' => 1,
        ]);

    }

    public function down()
    {

        $permissionId = $this->db->table('pm_permissions')->where('permission', 'manage_schoolspace')->get()->getRow()->id;
        $this->db->table('pm_permissions')->where('id', $permissionId)->delete();

        $this->db->table('pm_permissions_roles')->where('id_permission', $permissionId)->delete();

    }
}
