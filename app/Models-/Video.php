<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';

    //Relación One to Many
    public function comments(){
        return $this->hasMany('App\Models\Comment')->orderBy('id','desc');
    }
    //Relación de Muchos a Uno
    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }
}

