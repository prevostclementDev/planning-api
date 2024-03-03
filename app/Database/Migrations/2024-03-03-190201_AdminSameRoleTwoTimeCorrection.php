<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdminSameRoleTwoTimeCorrection extends Migration
{
    public function up()
    {
        $this->db->table(' pm_permissions_roles')->where('id_role',1)->where('id_permission',1)->delete();
        $this->db->table(' pm_permissions_roles')->insert(['id_role' => 1, 'id_permission' => 1]);
    }

    public function down()
    {
        $this->db->table(' pm_permissions_roles')->where('id_role',1)->where('id_permission',1)->delete();

        $this->db->table(' pm_permissions_roles')->insert(['id_role' => 1, 'id_permission' => 1]);
        $this->db->table(' pm_permissions_roles')->insert(['id_role' => 1, 'id_permission' => 1]);
    }
}
