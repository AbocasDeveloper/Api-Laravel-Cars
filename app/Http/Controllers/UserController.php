<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    
    public function register(Request $request){

        //Recoger variables por POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        //Si los datos no son NULL, creamos el usuario
        if(!is_null($email) && !is_null($name) && !is_null($surname) && !is_null($password)){

            //Creamos el usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            //Ciframos la contraseña
            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //Comprobamos si el usuario ya existe
            $isset_user = User::where('email', '=', $email)->first();

            if(empty($isset_user)){ //Si la variable esta vacia, no existe ese usuario
                //Guardamos el usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario creado correctamente'
                );
            }
            else{
                //No guardamos el usuario, ya que existe
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario no creado, esta duplicado'
                );
            }
        }
        else{
            //Ha llegado algún campo NULL
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Usuario no creado, algun campo ha llegado NULL'
            );
        }

        return response()->json($data, 200);
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        //Recibimos los datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

        //Cifrar la contraseña
        $pwd = hash('sha256', $password);

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuth->signup($email, $pwd);
        }
        elseif($getToken != null){
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
        }
        else{
            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por POST'
            );
        }

        return response()->json($signup, 200);
    }
}
