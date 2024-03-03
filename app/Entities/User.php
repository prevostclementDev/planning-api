<?php

namespace App\Entities;

use App\Libraries\ResponseFormat;
use App\Models\CoursesModel;
use App\Models\RolesModel;
use App\Models\SchoolSpacesModel;
use App\Models\UserSkillsModel;
use App\Models\UserUnavailabilitiesModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Entity\Entity;
use CodeIgniter\Model;
use JetBrains\PhpStorm\ArrayShape;

class User extends Entity
{
    protected $datamap = [
        'id' => null,
        'first_name' => null,
        'last_name' => null,
        'mail' => null,
        'profile_picture' => null,
        'roles' => null,
        'password' => null,
        'id_school_space' => null,
        'last_connexion' => null
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    // ###############################################################################################
    //                                          ROLES / PERMISSIONS
    // ###############################################################################################

    // get user role
    public function getRoles(): object|null
    {
        $roleModel = new RolesModel();
        return $roleModel->find($this->attributes['roles']);
    }

    // if user have permissions []
    public function canDo(array $permissions): bool
    {

        $currentUserPermissions = $this->getRoles()->getPermissions();

        $nbPermissions = count($permissions);

        foreach ( $currentUserPermissions as $permission ) {
            if ( in_array($permission['permission'], $permissions) ) $nbPermissions--;
        }

        if ( $nbPermissions === 0 ) return true;

        return false;

    }

    // ###############################################################################################
    //                                           UNAVAILABILITIES
    // ###############################################################################################

    // get unavailabilities of user
    public function getUnavailabilities() : array|false {

        $UserUnavailabilitiesModel = new UserUnavailabilitiesModel();
        $UserUnavailabilitiesModel->where('id_teacher',$this->attributes['id']);

        try {
            $unavailabilities = $UserUnavailabilitiesModel->findAll();
        } catch (DatabaseException $databaseException) {
            return false;
        }

        return $unavailabilities;

    }

    // create unavailabilities
    public function createUnavailabilities(array $data) : ResponseFormat {

        if ( ! isset($data['id_teacher']) ) $data['id_teacher'] = $this->attributes['id'];

        $responseFormat = new ResponseFormat();

        $insert = insertNewData('UserUnavailabilitiesModel', $data);

        if ( ! $insert['status'] ) {
            $responseFormat
                ->setError(400,'Erreur pendant la création de la donnée')
                ->addData($insert['errors'],'errors');
        }

        return $responseFormat;

    }

    // delete Unavailabilities
    public function deleteUnavailabilities(int $idUnavailabilities): ResponseFormat
    {

        $responseFormat = new ResponseFormat('Indisponibilité n\'existe pas');
        $unavailabilitiesModel = new UserUnavailabilitiesModel();
        $unavailabilities = $unavailabilitiesModel
            ->where('id_teacher',$this->attributes['id'])
            ->find($idUnavailabilities);

        if ( is_null($unavailabilities) ) {

            $responseFormat->setError(404);

        } else {

            try {
                $unavailabilitiesModel->delete($idUnavailabilities);
            } catch ( DatabaseException $databaseException ) {
                $responseFormat->setError();
            }

        }

        return $responseFormat;

    }

    // delete Unavailabilities
    public function UpdateUnavailabilities(array $data,int $idUnavailabilities) : ResponseFormat {

        $responseFormat = new ResponseFormat('Indisponibilité n\'existe pas');

        if ( empty( $data ) ) {
            return $responseFormat->setError(400,'Donnée(s) manquante(s)');
        }

        $unavailabilitiesModel = new UserUnavailabilitiesModel();
        $unavailabilities = $unavailabilitiesModel
            ->where('id_teacher',$this->attributes['id'])
            ->find($idUnavailabilities);

        if ( is_null($unavailabilities) ) {

            $responseFormat->setError(404);

        } else {

            $unavailabilities->fill($data);
            $responseFormat = updateData($unavailabilities,'UserUnavailabilitiesModel');

        }

        return $responseFormat;

    }


    // ###############################################################################################
    //                                               SKILLS
    // ###############################################################################################

    // add teacher skill
    public function addSkills(int $idCourse): ResponseFormat {

        $responseFormat = new ResponseFormat('Le cours n\'existe pas');
        $courseModel = $this->queryOnSchoolSpace('CoursesModel');
        $course = $courseModel->find($idCourse);

        if ( is_null($course) ) {
            return $responseFormat->setError(404);
        }

        $UserSkillModel = new UserSkillsModel();

        $save = insertNewData('UserSkillsModel',[
            'id_teacher' => $this->attributes['id'],
            'id_course' => $idCourse,
        ]);

        return $save['response'];

    }

    // get teacher skill
    #[ArrayShape(['result' => "array|null", 'model' => "\App\Models\UserSkillsModel"])]
    public function getSkills(int $pagination = 25, array $params = []): array {

        $UserSkillModel = new UserSkillsModel();

        $skillsUser = $UserSkillModel
            ->select('pm_courses.name, pm_courses.id')
            ->join('pm_courses','pm_courses.id = pm_teachers_skills.id_course')
            ->where('id_teacher',$this->attributes['id'])
            ->paginate(
                $pagination,
                'default',
                (! isset($params['page'])) ? null : $params['page']
            );

        return [ 'result' => $skillsUser, 'model' => $UserSkillModel ];

    }

    // delete skill to teacher
    public function deleteSkills(int $idCourse): ResponseFormat {

        $responseFormat = new ResponseFormat('Le cours n\'existe pas');
        $courseModel = $this->queryOnSchoolSpace('CoursesModel');
        $course = $courseModel->find($idCourse);

        if ( is_null($course) ) {
            return $responseFormat->setError(404);
        }

        $UserSkillModel = new UserSkillsModel();

        try {
            $UserSkillModel
                ->where('id_teacher',$this->attributes['id'])
                ->where('id_course',$idCourse)
                ->delete();
        } catch (DatabaseException $exception) {
            $responseFormat->setError();
        }

        return $responseFormat;

    }

    // ###############################################################################################
    //                                                SCHOOLSPACE
    // ###############################################################################################

    // get schoolspace of user
    public function getLinkSchoolSpaces() : object|null
    {
        $schoolspaceModel = new SchoolSpacesModel();
        return $schoolspaceModel->find($this->attributes['id_school_space']);
    }

    // Create where clause query on schoolspace of user ( default id_school_space column : id_school_space)
    public function queryOnSchoolSpace(string $model, string $matchIdColumn = 'id_school_space') : false|Model {

        $model = model($model);

        if ( isset( $this->attributes['id_school_space'] ) ) {
            $model->where($matchIdColumn, $this->attributes['id_school_space']);
        } else {
            return false;
        }

        return $model;

    }

}
