<?php

namespace App\Controllers;

use App\Entities\Schoolspace;
use App\Libraries\ResponseFormat;
use App\Models\SchoolSpacesModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class SchoolSpacesController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat();
    }

    public function create(): ResponseInterface {

        // GET DATA FOR CREATE NEW SCHOOL SPACE
        $dataUser = $this->request->getPost(['first_name','last_name','mail','password','profile_picture']);
        $dataSchoolSpace = $this->request->getPost(['name','profile_picture']);

        // INSERT SCHOOL SPACE
        $saveSchoolSpace = insertNewData( 'SchoolSpacesModel' , $dataSchoolSpace );
        if( ! $saveSchoolSpace['status'] ) {
            return $this->respond( $saveSchoolSpace['response']->getResponse() , $saveSchoolSpace['response']->getCode()  );
        }

        // ADD ADDITIONAL DATA TO USER
        $dataUser['id_school_space'] = $saveSchoolSpace['id'];
        $dataUser['roles'] = 1;

        // INSERT USER ( delete school space if user insert failure )
        $saveUser = insertNewData( 'UserModel' , $dataUser, [
            ['model' => 'SchoolSpacesModel', 'id' =>  $saveSchoolSpace['id'] ]
        ] );
        if( ! $saveUser['status'] ) {
            return $this->respond( $saveUser['response']->getResponse() , $saveUser['response']->getCode()  );
        }

        // respond success
        return $this->respond(
            $this->responseFormat->setCode(201)->getResponse(),
            201);

    }

    public function get() : ResponseInterface {

        $schoolspace = CURRENT_USER->getLinkSchoolSpaces();

        return $this->respond(
            $this->responseFormat->setCode(200)->addData([
                'name' => $schoolspace->name,
                'profile_picture' => $schoolspace->profile_picture,
            ])->getResponse(),
            200
        );
    }

    public function update() : ResponseInterface {

        $post = $this->request->getRawInput();

        $data = allowDataPicker($post,['name','profile_picture']);

        if ( empty( $data ) ) {
            return $this->respond(
                $this->responseFormat->setError(400,'Donnée(s) non valide')->getResponse(),
                200
            );
        }

        $schoolspace = CURRENT_USER->getLinkSchoolSpaces();
        $schoolspaceModel = new SchoolSpacesModel();

        $schoolspace->fill($data);

        if ( $schoolspace->hasChanged() ) {
            $schoolspaceModel->save($schoolspace);
        } else {
            $this->responseFormat->setCode(304)->addData('Données déjà à jour');
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );

    }

    public function delete() : ResponseInterface {

        $schoolspace = CURRENT_USER->getLinkSchoolSpaces();

        if ( $schoolspace->deleteHim() ) {

            return $this->respond(
                $this->responseFormat->getResponse(),
                200
            );

        }

        return $this->respond(
            $this->responseFormat->setError()->getResponse(),
            500
        );


    }

}