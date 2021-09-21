<?php

namespace App\Helpers;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = 'esta-es-mi-clave-secreta-*-1222344554324986';
    }

    public function signup($email, $password, $getToken = null){

        $user = User::where(
                array(
                    'email' => $email,
                    'password' => $password
                ))->first();

        //Comprobamos el usuario, para loguearlo
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        if($signup){
            //Generar el TOKEN y devolverlo
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(), //Cuando se ha creado el token
                'exp' => time() + (7 * 24 * 60 * 60) //Cuanto va a durar el token (una semana)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256'); //TOKEN
            $decoded = JWT::decode($jwt, $this->key, array('HS256')); //Objeto del usuario identificado

            if(is_null($getToken)){
                return $jwt;
            }
            else{
                return $decoded;
            }
        }
        else{
            //Devolver error
            return array(
                'status' => 'error',
                'message' => 'Login fallido'
            );
        }
    }

    public function checkToken($jwt, $getIdentity = false){

        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }
        else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }
}

?>
