<?php

namespace App\Libraries;

class ResponseFormat
{

    public static bool $headerStackAsSet = false;

    // response
    private array $response = [
        'statusBool' => true,
        'status' => 'success',
        'code' => '200',
        'message' => 'OK - Requête réussie',
        'data' => []
    ];

    // data of code message
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

    // 404 custom message
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

    // *******************************
    //             GETTER
    // *******************************

    // return array response
    public function getResponse() : array {
        return $this->response;
    }

    // get code response set
    public function getCode() : int {
        return $this->response['code'];
    }

    // *******************************
    //             SETTER
    // *******************************

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
    public function setError(int $code = 500, mixed $details = null,?string $type = null): static
    {
        $this->response['status'] = 'error';
        $this->response['statusBool'] = false;
        $this->setCode($code);

        if ( ! is_null($details) ) {
            $this->addData($details,'details');
        }

        if ( ! is_null($type) ) {
            $this->addData($type,'TypeError');
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

    // ***********************************************
    //                  HEADER OPTIONS
    // ***********************************************
    // Global set header
    public static function setHeader(string $key, string $value): void {
        header("{$key}: {$value}");
    }

    // set allow method
    public static function setAllowMethodHeader(string $method = 'GET, POST, OPTIONS, PUT, DELETE') : void {
        self::setHeader('Access-Control-Allow-Methods',$method);
    }

    // set allow control header
    public static function setAllowControlHeader(string $allowControl = 'Content-Type, X-CSRF-TOKEN', string $allowCredentials = "true") : void {
        self::setHeader('Access-Control-Allow-Headers',$allowControl);
        self::setHeader('Access-Control-Allow-Credentials',$allowCredentials);
    }

    // set allow origin
    public static function setAllowOriginHeader(string $urlAllow, bool $auto = false) : void {

        if ( $auto ) {

            switch (base_url()) {

                case 'http://localhost/site/gestionnaire-planning-api/public/':
                    $urlAllow = 'http://localhost:3000';
                    break;
                case 'https://api.doriane.app/':
                    $urlAllow = 'https://doriane.app';
                    break;
                case 'https://preprod.api.doriane.app/':
                    $urlAllow = 'https://preprod.doriane.app';
                    break;
            }

            var_dump($urlAllow,'test');
            die();

        }

        var_dump($urlAllow);
        die();

        self::setHeader('Access-Control-Allow-Origin',$urlAllow);
    }

    // set all header auto
    public static function setAllDefaultHeader() : void {
        self::setAllowOriginHeader('',true);
        self::setAllowControlHeader();
        self::setAllowMethodHeader();
        self::$headerStackAsSet = true;
    }

}