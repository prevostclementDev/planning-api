<?php

namespace App\Libraries;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\Cookie\Cookie;

class Authentification
{

    // generate JWT
    public function generateToken(string $mail, string $password): array
    {
        $user = $this->verifyUserId($mail,$password);

        // if bad connexion information
        if ( ! $user ) return array( 'status' => false, 'message' => 'Identifiant incorrect');

        // generate CSRF
        $csrf = bin2hex(random_bytes(35));

        // create payload, header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['user_id' => $user->id,'csrf' => $csrf,]);

        // encode payload, header
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // create signature
        $signature = $this->generateSignature($base64UrlHeader,$base64UrlPayload);
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $signature;

        // set jwt in cookie instance
        $cookie = new Cookie(
            'access_token',
            $jwt,
            [
                'expires'  => strtotime('+3 days', time()),
                'httponly' => true,
                'secure' => !((env('CI_ENVIRONMENT') === 'development')),
                'samesite' => 'Strict'
            ]
        );

        return array(
            'status' => true,
            'message' => 'success',
            'jwt' => $jwt,
            'csrf' => $csrf,
            'cookie' => $cookie,
        );

    }

    // verify if token is valide
    public function verifyToken(string $token,string $csrf) : bool {

        // decode
        $splitToken = $this->decodeToken($token);

        // if decode error
        if ( ! $splitToken ) {
            return false;
        }

        // regenerate signature for check
        $signature = $this->generateSignature($splitToken['header'],$splitToken['payload']);

        // if signature equals
        $validSignature =  $signature === $splitToken['signature'];

        // if csrf equals
        $validCSRF = isset($splitToken['decode']['payload']->csrf) && $splitToken['decode']['payload']->csrf === $csrf;

        // if all is valid
        if ( $validSignature && $validCSRF ) {
            return true;
        }

        return false;

    }

    // decode token
    public function decodeToken(string $token) : false|array {

        if ( empty($token) ) {
            return false;
        }

        $split = explode('.',$token);

        return [
            'header' => $split[0] ,
            'payload' => $split[1] ,
            'signature' => $split[2],
            'decode' => [
                'header' => json_decode( base64_decode( $split[0] ) ),
                'payload' => json_decode( base64_decode( $split[1] ) ),
            ]
        ];

    }

    // verify user auth (email, password)
    private function verifyUserId(string $mail, string $password) : false|object {

        $userModel = new UserModel();
        $user = $userModel->where('mail',$mail)->first();

        if (is_null($user)) return false;

        if ( !password_verify( $password, $user->password )) return false;

        return $user;

    }

    // generate signature
    private function generateSignature($header,$payload): bool|string
    {
        $signature = hash_hmac('sha256', $header . "." . $payload, env('SECRET_KEY'), true);

        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    }

}