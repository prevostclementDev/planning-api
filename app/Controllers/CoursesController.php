<?php

namespace App\Controllers;

use App\Libraries\ResponseFormat;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

// Global course
class CoursesController extends BaseController
{

    use ResponseTrait;

    public ResponseFormat $responseFormat;

    // init
    public function __construct(){
        helper('database');
        $this->responseFormat = new ResponseFormat('Le cours n\'existe pas');
    }

    // create course
    public function create(): ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post, ['name','hours_required','color']);

        $data['id_school_space'] = CURRENT_USER->id_school_space;

        $saveCourse = insertNewData('CoursesModel', $data);

        return $this->respond(
            $saveCourse['response']->getResponse(),
            $saveCourse['response']->getCode()
        );

    }

    // get course
    public function get(): ResponseInterface {

        $params = $this->request->getGet(['page','search']);

        $coursesModel = CURRENT_USER->queryOnSchoolSpace('CoursesModel');

        if ( isset($params['search']) && ! empty($params['search']) ) {
            $coursesModel->searchCourses($params['search']);
        }

        $data = $coursesModel->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'courses')->addData(createPager($coursesModel),'pagination')->getResponse(),
            200
        );

    }

    // get course by ID
    public function getOne(int $id): ResponseInterface {

        $coursesModel = CURRENT_USER->queryOnSchoolSpace('CoursesModel');
        $courses = $coursesModel->find($id);

        if ( is_null($courses) ) {
            $this->responseFormat->setError(404);
        } else {
            $this->responseFormat->addData($courses);
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // update course by ID
    public function update(int $id): ResponseInterface {

        $post = get_object_vars( $this->request->getJSON() );
        $data = allowDataPicker($post , [ 'name' , 'hours_required' , 'color' ]);

        if ( empty( $data ) ) {
            return $this->respond(
                $this->responseFormat->setError(400,'Donnée(s) non valide')->getResponse(),
                400
            );
        }

        $courseModel = CURRENT_USER->queryOnSchoolSpace('CoursesModel');
        $course = $courseModel->find($id);

        if ( is_null($course) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        $course->fill($data);

        $this->responseFormat = updateData($course,'CoursesModel');

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // Delete course by ID
    public function delete(int $id): ResponseInterface {

        $courseModel = CURRENT_USER->queryOnSchoolSpace('CoursesModel');
        $course = $courseModel->find($id);

        if ( is_null($course) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        try {
            $courseModel->delete($id);
        } catch (DatabaseException $databaseException) {
            $this->responseFormat->setError();
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

}

