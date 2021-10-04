<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    public function index(Request $request){

        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();

        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            echo "INDEX DE CARCONTROLLER AUTENTICADO"; die();
        }
        else{
            echo "NO AUTENTICADO EN INDEX DE CARCONTROLLER..."; die();
        }
    }

    //Método que nos guarda un coche, recibiendo los datos anteriormente
    public function store(Request $request){
        //Autenticación
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();

        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Recoger datos por POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true); //Al escribir true, nos devuelve un array

            //Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true); 
            
            //Validamos los datos que hemos recibido por POST
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            //Vemos si tenemos errores en la validacion
            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }

            //Guardar el coche
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(
                'car' => $car,
                'status' => 'success',
                'code' => 200
            );
        }
        else{
            //Devolvemos un error
            $data = array(
                'message' => 'Login incorrecto!',
                'status' => 'error',
                'code' => 400
            );
        }
        return response()->json($data, 200);
    }
} //Final de la clase
