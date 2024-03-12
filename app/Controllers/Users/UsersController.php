<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormat;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class UsersController extends BaseController
{

    use ResponseTrait;

    private ResponseFormat $responseFormat;

    public function __construct()
    {
        helper('database');
        $this->responseFormat = new ResponseFormat('L\'utilisateur n\'existe pas.');
    }

    // create user
    public function create(): ResponseInterface
    {

        $post = get_object_vars($this->request->getJSON());
        $dataUser = allowDataPicker( $post , [ 'first_name', 'last_name', 'mail', 'password', 'profile_picture','roles' ] );

        $dataUser['id_school_space'] = CURRENT_USER->id_school_space;

        $insertion = insertNewData('UserModel', $dataUser);

        return $this->respond(
            $insertion['response']->getResponse(),
            $insertion['response']->getCode(),
        );

    }

    // GET LIST OF USER (Paginate by 25)
    // args page to navigate
    // search to find by string, email, name etc...
    public function get() : ResponseInterface {

        $params = $this->request->getGet(['page','search','roles']);

        $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');

        if ( ! is_null($params['search']) ) {
            $userModel->searchUser($params['search']);
        }

        if ( ! is_null($params['roles']) ) {
            $userModel->where('roles',$params['roles']);
        }

        $data = $userModel->paginate(25,'default',( is_null($params['page']) ) ? null : $params['page']);

        return $this->respond(
            $this->responseFormat->addData($data,'users')->addData(createPager($userModel),'pagination')->getResponse(),
            200
        );

    }

    // GET ONE USERS
    // AUTH IF current user can see_all_users OR current user search self
    public function getOne(int $id) : ResponseInterface {


        if ( CURRENT_USER->id == $id || CURRENT_USER->canDo(['see_all_users']) ) {

            $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');
            $user = $userModel->find($id);

            if ( is_null($user) ) {
                $this->responseFormat->setError(404);
            } else {
                $this->responseFormat->addData($user);
            }

            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode()
            );

        }

        return $this->respond(
            $this->responseFormat->setError(403),
            403
        );

    }

    // UPDATE USER
    public function update(int $id) : ResponseInterface {

        $canManageUser = CURRENT_USER->canDo(['manage_users']);

        if ( CURRENT_USER->id === $id || $canManageUser ) {

            $post = get_object_vars($this->request->getJSON());
            $data = allowDataPicker($post,[
                'first_name',
                'last_name',
                'profile_picture',
                'mail',
            ]);

            if ( $canManageUser && isset($post['roles']) ) {
                $data['roles'] = $post['roles'];
            }

            if ( empty( $data ) ) {
                return $this->respond(
                    $this->responseFormat->setError(400,'Donnée(s) non valide')->getResponse(),
                    400
                );
            }

            $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');
            $user = $userModel->find($id);

            if ( is_null($user) ) {
                return $this->respond(
                    $this->responseFormat->setError(404)->getResponse(),
                    404
                );
            }

            $user->fill($data);

            $this->responseFormat = updateData($user,'UserModel');

            return $this->respond(
                $this->responseFormat->getResponse(),
                $this->responseFormat->getCode()
            );

        }

        return $this->respond(
          $this->responseFormat->setError(403,'Vous n\'avez pas les autorisations suffisantes')->getResponse()
        );

    }

    // DELETE USER IF EXIST
    public function delete(int $id) : ResponseInterface {

        $userModel = CURRENT_USER->queryOnSchoolSpace('UserModel');
        $user = $userModel->find($id);

        if ( is_null($user) ) {
            return $this->respond(
                $this->responseFormat->setError(404)->getResponse(),
                404
            );
        }

        try {
            $userModel->delete($id);
        } catch (DatabaseException $databaseException) {
            $this->responseFormat->setError();
        }

        return $this->respond(
            $this->responseFormat->getResponse(),
            $this->responseFormat->getCode()
        );


    }

    // Return permissions for user
    public function getPermissions(int $id) : ResponseInterface {

        if ( CURRENT_USER->id == $id ) {

            $permissionsResult = CURRENT_USER->getRoles()->getPermissions();

            return $this->respond(
                $this->responseFormat->addData($permissionsResult,'permissions')->getResponse(),
            );

        }

        return $this->respond(
            $this->responseFormat->setError(403)->getResponse(),
            403
        );


    }

}

