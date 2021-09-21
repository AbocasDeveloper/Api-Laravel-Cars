<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;

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
}
