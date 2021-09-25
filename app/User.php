<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use Notifiable;


    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    |
    | - id
    | - name
    | - email
    | - password
    | - role  '0 - admin, 1 - editor'
    | - status '0 - inactive, 1 - active, 2 - blocked' 
    | - email_verified_at
    | - hash
    | - remember_token
    | - created_at
    | - updated_at
    |
    */


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'email_verified_at', 
        'hash', 
        'remember_token', 
        'created_at', 
        'updated_at',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime',
    ];


    /**
     * The model's default values for attributes.
     *
     * @var array
     */

    protected $attributes = [
        'role' => 1,
        'status' => 0,
    ];


   /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getRoleAttribute($value)
    {
        $role = [0 => 'admin', 1 => 'editor'];
        return $role[$value];
    }


    public function getStatusAttribute($value)
    {
        $status = [0 => 'inactive', 1 => 'active', 2 => 'blocked'];
        return $status[$value];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Get the scenes of user
     */
    public function scenes()
    {
        return $this->hasMany('App\Scene');
    }


    /**
     * Get custom markers
     */
    public function customMarkers()
    {
        return $this->hasMany('App\CustomMarker');
    }


}
