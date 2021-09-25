<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    |
    | - a-entity
    | - a-image
    | - a-sound
    | - a-video
    | - a-text
    | - a-gltf-model 
    |
    */

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get the marker of entity
     */
    public function marker()
    {
        return $this->belongsTo('App\Marker');
    }

}
