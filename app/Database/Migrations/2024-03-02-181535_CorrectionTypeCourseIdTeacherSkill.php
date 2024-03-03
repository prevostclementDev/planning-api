<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CorrectionTypeCourseIdTeacherSkill extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('pm_teachers_skills','id_course');

        $this->forge->addColumn('pm_teachers_skills',[
            'id_course' => [
                'type' => 'INT',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pm_teachers_skills','id_course');

        $this->forge->addColumn('pm_teachers_skills',[
            'id_course' => [
                'type' => 'date',
            ],
        ]);
    }
}
