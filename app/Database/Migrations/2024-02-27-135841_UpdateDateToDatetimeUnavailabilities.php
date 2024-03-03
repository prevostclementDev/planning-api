<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDateToDatetimeUnavailabilities extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pm_teachers_unavailabilities','start_date');
        $this->forge->dropColumn('pm_teachers_unavailabilities','end_date');

        $this->forge->addColumn('pm_teachers_unavailabilities',[
            'start_date' => [
                'type' => 'datetime',
            ],
            'end_date' => [
                'type' => 'datetime',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pm_teachers_unavailabilities','start_date');
        $this->forge->dropColumn('pm_teachers_unavailabilities','end_date');

        $this->forge->addColumn('pm_teachers_unavailabilities',[
            'start_date' => [
                'type' => 'date',
            ],
            'end_date' => [
                'type' => 'date',
            ]
        ]);
    }
}
