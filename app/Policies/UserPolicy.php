<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Only admin can list users.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function admin_index(User $user)
    {
        return $user->role === 'admin';
    }



    /**
     * Only admin can add users.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function admin_add(User $user)
    {
        return $user->role === 'admin';
    }



    /**
     * Determine whether the user can edit data.
     *
     * @param  \App\User  $user
     * @param  \App\User  $_user
     * @return boolean
     */
    public function edit(User $user, User $_user)
    {
        return $user->id === $_user->id;
    }



    public function admin_resend(User $user)
    {
        return $user->role === 'admin';
    }

    

    public function admin_delete(User $user, User $_user)
    {
        return $user->role === 'admin' && $user->id !== $_user->id;
    }
}
