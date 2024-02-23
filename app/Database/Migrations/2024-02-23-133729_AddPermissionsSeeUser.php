<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermissionsSeeUser extends Migration
{

    public function up()
    {
        $this->db->table('pm_permissions')->insert([
            'permission' => 'see_all_users'
        ]);

        $permissionId = $this->db->insertID();

        $this->db->table('pm_permissions_roles')->insert([
            'id_permission' => $permissionId,
            'id_role' => 1,
        ]);

        $this->db->table('pm_permissions_roles')->insert([
            'id_permission' => $permissionId,
            'id_role' => 3,
        ]);

    }

    public function down()
    {

        $permissionId = $this->db->table('pm_permissions')->where('permission', 'see_all_users')->get()->getRow()->id;
        $this->db->table('pm_permissions')->where('id', $permissionId)->delete();

        $this->db->table('pm_permissions_roles')->where('id_permission', $permissionId)->delete();

    }
}
