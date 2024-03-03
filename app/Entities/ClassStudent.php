<?php

namespace App\Entities;

use App\Libraries\ResponseFormat;
use App\Models\ClassUserModel;
use App\Models\ProgramsCoursesModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Entity\Entity;
use JetBrains\PhpStorm\ArrayShape;

class ClassStudent extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    // Add student to the class
    public function addStudent($idStudent,User $user){

        $student = $user->queryOnSchoolSpace('UserModel')->find($idStudent);
        $responseFormat = new ResponseFormat();

        if ( is_null($student) ) {

            $responseFormat->setError(404,'L\'utilisateur n`\'existe pas');

        } else {

            $save = insertNewData('ClassUserModel',[
                'id_class'  => $this->attributes['id'],
                'id_student' => $idStudent,
            ]);

            $responseFormat = $save['response'];

        }

        return $responseFormat;

    }

    // get all student from class
    #[ArrayShape(['result' => "array|null", 'model' => "\App\Models\ClassUserModel"])]
    public function getStudents(int $pagination = 25, array $params = []) : array {
        $classUserModel = new ClassUserModel();

        $data = $classUserModel
            ->select('
                pm_users.id as UserId, 
                pm_users.mail,
                pm_users.first_name,
                pm_users.last_name,
            ')
            ->join('pm_users','pm_users.id = id_student')
            ->join('pm_class','pm_class.id = id_class')
            ->where('id_class',$this->attributes['id'])
            ->paginate(
                $pagination,
                'default',
                (! isset($params['page'])) ? null : $params['page']
            );

        return ['result' => $data, 'model' => $classUserModel];

    }

    // delete student from class
    public function deleteStudent(int $idStudent): ResponseFormat {

        $classUserModel = new ClassUserModel();
        $responseFormat = new ResponseFormat();

        try {
            $classUserModel
                ->where('id_class',$this->attributes['id'])
                ->where('id_student',$idStudent)
                ->delete();
        } catch (DatabaseException $databaseException) {
            $responseFormat->setError();
        }

        return $responseFormat;

    }

}
