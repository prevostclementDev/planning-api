<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class TeacherSkillController extends BaseController
{

    use ResponseTrait;

    public ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('L\'utilisateur n\'existe pas.');
    }

    // add teacher skill
    public function create(int $idUser, int $idCourse): ResponseInterface
    {

        if ( $this->canAffectSkills($idUser) ) {

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $this->responseFormat = $user->addSkills($idCourse);

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // get teacher skills
    public function get(int $idUser): ResponseInterface
    {

        if ( $this->canAffectSkills($idUser) ) {

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $skills = $user->getSkills();

            $this->responseFormat
                ->addData($user,'user')
                ->addData($skills['result'],'skills')
                ->addData(createPager($skills['model']),'pagination');

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // delete teacher skill
    public function delete(int $idUser, int $idCourse): ResponseInterface
    {

        if ( $this->canAffectSkills($idUser) ) {

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $this->responseFormat = $user->deleteSkills($idCourse);

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode(),
        );

    }

    // check if current user can manage skills
    private function canAffectSkills(int $idUser) : bool {

        $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');
        $user = $userModel->find($idUser);

        // user do not exist
        if ( is_null($user) ) {
            $this->responseFormat->setError(404);
            return false;
        }

        // can update him skills
        $updateHimSkill = CURRENT_USER->id === $idUser && CURRENT_USER->canDo(['manage_user_own_skills']) ;

        // current user can update skill and user can have skill
        $updateOtherSkill =
            CURRENT_USER->id !== $idUser &&
            CURRENT_USER->canDo(['manage_users_skills']) &&
            $user->canDo(['manage_user_own_skills']);


        if ( ! $updateOtherSkill && ! $updateHimSkill ) {
            $this->responseFormat->setError(403,'Vous n\'avez pas les permissions. Ou le type de l\'utilisateur ne supporte pas cette information');
            return false;
        }

        return true;

    }

}
