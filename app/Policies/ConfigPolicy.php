<?php

namespace App\Policies;

use App\Config;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConfigPolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Only admin can config.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function index(User $user)
    {
        return $user->role === 'admin';
    }

}
