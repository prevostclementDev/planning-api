<?php

namespace App\Entities;

use App\Models\UserModel;
use CodeIgniter\Entity\Entity;

class Course extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    // Get all teacher for this course
    public function getTeacher(): array {
        $userModel = new UserModel();

        return $userModel
            ->select(User::$select)
            ->join('pm_teachers_skills','pm_teachers_skills.id_teacher = pm_users.id')
            ->where('pm_teachers_skills.id_course',$this->attributes['id'])
            ->findAll();
    }

}
