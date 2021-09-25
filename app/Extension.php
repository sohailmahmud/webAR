<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    |
    | - id
    | - type
    | - props    
    | - project_id
    |
    */


    public $timestamps = false;

    
    /*
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'props' => 'array'
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    public function scene()
    {
        return $this->belongsTo('App\Scene');
    }    

}
