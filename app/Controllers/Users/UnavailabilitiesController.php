<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use App\Models\UserModel;
use App\Models\UserUnavailabilitiesModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class UnavailabilitiesController extends BaseController
{

    use ResponseTrait;

    public ResponseFormat $responseFormat;

    public function __construct() {
        helper('database');
        $this->responseFormat = new ResponseFormat('L\'indisponibilité n\'existe pas');
    }

    // get unavailabilities
    public function get(int $idUser) : ResponseInterface {

        if ( $this->canAffectUnavailabilities($idUser) ) {

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $unavailability = $user->getUnavailabilities();

            if ( $unavailability === false ) {

                $this->responseFormat->setError();

            } else {

                $this->responseFormat->addData($unavailability,'unavailabilities');

            }

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // update unavailabilities
    public function update(int $idUser,int $idUnavailabilities): ResponseInterface
    {

        if ( $this->canAffectUnavailabilities($idUser) ) {

            $post = get_object_vars($this->request->getJSON());
            $data = allowDataPicker($post,[
                'start_date',
                'end_date',
                'weekday',
                'repetition',
                'repetition_end_date',
            ]);

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $this->responseFormat = $user->UpdateUnavailabilities($data , $idUnavailabilities);

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // create unavailabilities
    public function create(int $idUser) : ResponseInterface {

        if ( $this->canAffectUnavailabilities($idUser) ) {

            $post = get_object_vars($this->request->getJSON());
            $dataCreateUnavailability = allowDataPicker($post,[
                'start_date',
                'end_date',
                'weekday',
                'repetition_end_date',
                'repetition'
            ]);

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $this->responseFormat = $user->createUnavailabilities($dataCreateUnavailability);

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // delete unavailabilities
    public function delete(int $idUser, int $idUnavailabilities): ResponseInterface {

        if ( $this->canAffectUnavailabilities($idUser) ) {

            $userModel = new UserModel();
            $user = $userModel->find($idUser);

            $this->responseFormat = $user->deleteUnavailabilities($idUnavailabilities);

        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    // check if request is good for unavailabilities
    private function canAffectUnavailabilities(int $idUser) : bool {

        $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');
        $user = $userModel->find($idUser);

        // user do not exist
        if ( is_null($user) ) {
            $this->responseFormat->setError(404,'L\'utilisateur n\'existe pas.');
            return false;
        }

        // user support unavailabilites for him
        $updateHimUnavailabilities = CURRENT_USER->id === $idUser && CURRENT_USER->canDo(['manage_user_own_unavailabilities']) ;

        // user support unavailabilites and current user can manage
        $updateOtherUnavailabilities =
            CURRENT_USER->id !== $idUser &&
            CURRENT_USER->canDo(['manage_users_unavailabilities']) &&
            $user->canDo(['manage_user_own_unavailabilities']);


        if ( ! $updateHimUnavailabilities && ! $updateOtherUnavailabilities ) {
            $this->responseFormat->setError(403,'Vous n\'avez pas les permissions. Ou le type de l\'utilisateur ne supporte pas cette information');
            return false;
        }

        return true;

    }

}