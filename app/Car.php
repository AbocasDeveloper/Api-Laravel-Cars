<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $tabke = 'cars';

    //Relacion de muchos a uno (1 usuario puede tener muchos coches)
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
