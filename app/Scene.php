<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    |
    | - id
    | - title
    | - description
    | - type
    | - status
    | - editable
    | - params
    | - published_at
    | - created_at
    | - updated_at
    | - user_id
    |
    */


     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * The model's default values for attributes.
     *
     * @var array
     */

    protected $attributes = [
        'type' => 's'
    ];


   /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getTypeAttribute($value)
    {
        $type = ['s' => 'single', 'b' => 'bundle'];
        return $type[$value];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Get the user of scene
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


    /**
     * Get the markers of scene
     */
    public function markers()
    {
        return $this->hasMany('App\Marker');
    }

    public function extensions()
    {
        return $this->hasMany('App\Extension');
    }
}
