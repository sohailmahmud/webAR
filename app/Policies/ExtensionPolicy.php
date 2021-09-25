<?php

namespace App\Policies;

use App\User;
use App\Extension;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExtensionPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->role == 'admin') {
            return true;
        }
    }



    /**
     * CREATOR POLICIES
     */

    public function edit(User $user, Extension $extension)
    {
        return $user->id == $extension->scene->user_id;
    }



    public function delete(User $user, Extension $extension)
    {
        return $user->id == $extension->scene->user_id;
    }

}
