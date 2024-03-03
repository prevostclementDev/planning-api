<?php

namespace App\Controllers\Programs;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\ProgramsCoursesModel;
use App\Models\ProgramsModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class ProgramsCoursesController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;
    private ProgramsModel $programsModel;

    // init
    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('Le programme n\'existe pas');
    }

    // add course in a program
    public function addCourse(int $idProgram, int $idCourse): ResponseInterface {

        $program = CURRENT_USER->queryOnSchoolSpace('ProgramsModel')->find($idProgram);

        if ( is_null($program) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $this->responseFormat = $program->addCourse($idCourse,CURRENT_USER);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // get all course link to a program
    public function getCourse(int $idProgram): ResponseInterface
    {

        $program = CURRENT_USER->queryOnSchoolSpace('ProgramsModel')->find($idProgram);

        if ( is_null($program) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $courses = $program->getCourses();

        $this->responseFormat
            ->addData($courses['result'],'courses')
            ->addData(createPager($courses['model']),'pagination');

        return $this->respond(
            $this->responseFormat->getResponse(),
            200
        );

    }

    // remove course from a program
    public function removeCourse(int $idProgram, int $idCourseLink): ResponseInterface {

        $program = CURRENT_USER->queryOnSchoolSpace('ProgramsModel')->find($idProgram);

        if ( is_null($program) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $this->responseFormat = $program->deleteCourse($idCourseLink);

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

}
