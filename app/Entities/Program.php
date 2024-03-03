<?php

namespace App\Entities;

use App\Libraries\ResponseFormat;
use App\Models\ProgramsCoursesModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Entity\Entity;
use JetBrains\PhpStorm\ArrayShape;

class Program extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];


    // ###############################################################################################
    //                                          COURSE
    // ###############################################################################################

    // add course to current program
    public function addCourse(int $idCourse,User $user) : ResponseFormat {

        $course = $user->queryOnSchoolSpace('CoursesModel')->find($idCourse);

        $responseFormat = new ResponseFormat();

        if  ( is_null($course) ) {
            $responseFormat->setError(404,'Le cours n\'existe pas');
        } else {

            $save = insertNewData('ProgramsCoursesModel',[
                'id_programs' => $this->attributes['id'],
                'id_course' => strval($idCourse)
            ]);

            $responseFormat = $save['response'];

        }

        return $responseFormat;

    }

    // get all course from program
    #[ArrayShape(['result' => "array|null", 'model' => "\App\Models\ProgramsCoursesModel"])]
    public function getCourses(int $pagination = 25, array $params = []): array {
        $ProgramCoursesModel = new ProgramsCoursesModel();

        $data = $ProgramCoursesModel
            ->select('pm_courses.id as idCourse, pm_courses.name as nameCourse, pm_courses.hours_required, pm_courses.color, pm_schools_programs.name as nameProgram, ')
            ->join('pm_courses','pm_courses.id = id_course')
            ->join('pm_schools_programs','pm_schools_programs.id = id_programs')
            ->where('id_programs',$this->attributes['id'])
            ->paginate(
                $pagination,
                'default',
                (! isset($params['page'])) ? null : $params['page']
            );

        return ['result' => $data, 'model' => $ProgramCoursesModel];

    }

    // delete course of program
    public function deleteCourse(int $idCourse) : ResponseFormat {

        $programCourseModel = new ProgramsCoursesModel();
        $responseFormat = new ResponseFormat();

        try {
            $programCourseModel
                ->where('id_programs',$this->attributes['id'])
                ->where('id_course',$idCourse)
                ->delete();
        } catch (DatabaseException $databaseException) {
            $responseFormat->setError();
        }

        return $responseFormat;

    }

}
