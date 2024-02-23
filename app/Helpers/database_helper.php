<?php

use CodeIgniter\Model;
use JetBrains\PhpStorm\ArrayShape;

if ( ! function_exists('insertNewData') ) {

    #[ArrayShape(['status' => "", 'errors' => "", 'id' => ""])]
    function insertNewData(String $model, array $data, array $onFailure = []): array
    {
        $status = true;
        $errors = [];
        $id = 0;
        $response = new \App\Libraries\ResponseFormat();
        $response->setCode(201);

        $model = model($model);

        try {
            $id = $model->insert($data);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {

            $status = false;
            $response->setError();

        }

        if ( $id === false ) {

            if ( ! empty($onFailure) ) {

                foreach ( $onFailure as $deleteElement ) {

                    $deleteModel = model($deleteElement['model']);

                    $deleteModel->delete($deleteElement['id']);
                    $deleteModel->where('id',$deleteElement['id'])->purgeDeleted();

                }

            }

            $status = false;
            $errors = $model->errors();

            $response->setError(400)->addData($errors);
        }

        return [ 'status' => $status, 'errors' => $errors, 'id' => $id, 'response' => $response ];
    }

}

if ( ! function_exists('allowDataPicker') ) {

    function allowDataPicker (array $post, array $allowData) : array {

        $render = array();

        foreach ( $post as $key => $value ) {

            if ( isset( $allowData[$key] ) ) {

                $render[$key] = $value;

            }

        }

        return $render;

    }

}

if ( ! function_exists('createPager') ) {

    #[ArrayShape(['current_page' => "int", 'page_count' => "int", 'per_page' => "int", 'total' => "int", 'next_uri' => "\CodeIgniter\HTTP\URI|null|string"])]
    function createPager(Model $model): array
    {
        return [
            'current_page' => $model->pager->getCurrentPage(),
            'page_count' => $model->pager->getPageCount(),
            'per_page' => $model->pager->getPerPage(),
            'total' => $model->pager->getTotal(),
            'next_uri' => $model->pager->getNextPageURI(),
        ];
    }

}