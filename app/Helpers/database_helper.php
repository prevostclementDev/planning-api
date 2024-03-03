<?php

use CodeIgniter\Entity\Entity;
use CodeIgniter\Model;
use JetBrains\PhpStorm\ArrayShape;

if ( ! function_exists('insertNewData') ) {

    // insert new data and handle all error
    #[ArrayShape(['status' => "", 'errors' => "", 'id' => "", 'response' => \App\Libraries\ResponseFormat::class])]
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

            $response->setError(400)->addData($errors,'errors');
        }

        return [ 'status' => $status, 'errors' => $errors, 'id' => $id, 'response' => $response ];
    }

}

if ( ! function_exists('updateData') ) {

    // update default function data
    function updateData(Entity $entity,string $model) : \App\Libraries\ResponseFormat {
        $model = model($model);
        $responseFormat = new \App\Libraries\ResponseFormat();

        if ( $entity->hasChanged() ) {
            $model->save($entity);

            if ( empty($model->errors()) ) {
                $responseFormat->addData('Information mise à jour','details')->addData($entity,'object');
            } else {
                $responseFormat->setError(400)->addData($model->errors(),'errors')->addData($entity,'object');
            }

        } else {
            $responseFormat->setCode(304);
        }

        return $responseFormat;

    }

}

if ( ! function_exists('allowDataPicker') ) {

    // get all field require available in $post
        function allowDataPicker (array $post, array $allowData) : array {

        $render = array();

        foreach ( $post as $key => $value ) {

            if ( in_array($key, $allowData) ) {

                $render[$key] = $value;

            }

        }

        return $render;

    }

}

if ( ! function_exists('createPager') ) {

    // with model return standard array for pagination
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