<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateStatusTypePlanningTable extends Migration
{
    public function up()
    {

        $this->forge->modifyColumn("pm_plannings",[
            "status" => [
                "type" => "enum('draft', 'in_validation', 'publish')",
                'null' => false
            ]
        ]);

    }

    public function down()
    {
        $this->forge->modifyColumn("pm_plannings",[
            "status" => [
                "type" => "enum('draf', 'in_validation', 'publish')",
                'null' => true
            ]
        ]);
    }
}
