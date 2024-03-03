<?php

namespace App\Libraries;

class ResponseFormat
{

    private array $response = [
        'status' => 'success',
        'code' => '200',
        'message' => 'OK - Requête réussie',
        'data' => []
    ];

    private array $codeMessage = array(
        // Codes de succès
        200 => "OK - Requête réussie",
        201 => "Created - Ressource créée avec succès",
        204 => "No Content - Pas de contenu à renvoyer",

        // Codes d'erreur courants
        400 => "Bad Request - La requête est incorrecte",
        401 => "Unauthorized - Authentification requise",
        403 => "Forbidden - Accès refusé",
        404 => "Not Found - Ressource non trouvée",
        405 => "Method Not Allowed - Méthode non autorisée",
        500 => "Internal Server Error - Erreur interne du serveur",
        502 => "Bad Gateway - Mauvaise passerelle",
        503 => "Service Unavailable - Service non disponible",

        // Autres codes d'erreur possibles
        301 => "Moved Permanently - Ressource déplacée de manière permanente",
        302 => "Found - Ressource trouvée",
        303 => "See Other - Redirection vers une autre ressource",
        304 => "Not Modified - Aucune modification",
        406 => "Not Acceptable - Requête non acceptable",
        408 => "Request Timeout - Délai d'attente de la requête dépassé",
        409 => "Conflict - Conflit de requêtes",
        410 => "Gone - Ressource n'est plus disponible",
        413 => "Payload Too Large - Charge de la requête trop importante",
        415 => "Unsupported Media Type - Type de média non supporté",
        429 => "Too Many Requests - Trop de requêtes",
    );

    private ?string $set404CustomDetails = null;

    // default set creation
    public function __construct(string $notFoundMessage = null,array $data = null, int $code = null, string $status = null, string $message = null)
    {
        if ( ! is_null($data) ) {

            foreach ($data as $key => $value ) {
                $this->response['data'][$key] = $value;
            }

        };

        if ( ! is_null($notFoundMessage) ) $this->set404CustomDetails = $notFoundMessage;

        if ( ! is_null($code) ) $this->response['code'] = $code;
        if ( ! is_null($status) ) $this->response['status'] = $status;
        if ( ! is_null($message) ) $this->response['message'] = $message;

        return $this;
    }

    // return array response
    public function getResponse() : array {
        return $this->response;
    }

    // get code response set
    public function getCode() : int {
        return $this->response['code'];
    }

    // add data to response
    public function addData(mixed $data,string $key = null): static
    {

        if (is_null($key)) {
            $this->response['data'][] = $data;
        } else {
            $this->response['data'][$key] = $data;
        }


        return $this;
    }

    // set response in error
    public function setError(int $code = 500, mixed $details = null): static
    {
        $this->response['status'] = 'error';
        $this->setCode($code);

        if ( ! is_null($details) ) {

            $this->addData($details,'details');

        }

        return $this;
    }

    // set code
    public function setCode(int $code) : static {
        $this->response['code'] = $code;
        $this->setMessageByCode($code);

        if ( ! is_null($this->set404CustomDetails) && $code === 404 ) {
            $this->addData($this->set404CustomDetails,'details');
        }

        return $this;
    }

    // set global message
    public function setMessage(string $message) : static {
        $this->response['message'] = $message;
        return $this;
    }

    // set message by code
    public function setMessageByCode(int $code = null) : static {

        if ( is_null($code) ) {
            $code = $this->response['code'];
        }

        $this->response['message'] = (isset($this->codeMessage[$code])) ? $this->codeMessage[$code] : '';

        return $this;

    }

}