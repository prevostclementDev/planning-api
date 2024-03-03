<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldRepetitionEndDate extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pm_teachers_unavailabilities',[
            'repetition_end_date' => [
                'type' => 'date',
                'null' => true,
            ],
        ]);

    }

    public function down()
    {
        $this->forge->dropColumn('pm_teachers_unavailabilities','repetition_end_date');
    }
}
