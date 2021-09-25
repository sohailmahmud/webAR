<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */

    public $timestamps = false;


    /**
     * Get the scene
     */
    public function scene()
    {
        return $this->belongsTo('App\Scene');
    }


    /**
     * Get the entities
     */
    public function entities()
    {
        return $this->hasMany('App\Entity');
    }
}
