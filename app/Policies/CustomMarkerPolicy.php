<?php

namespace App\Policies;

use App\User;
use App\CustomMarker;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomMarkerPolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Determine whether the user can edit the custom marker.
     *
     * @param  \App\User  $user
     * @param  \App\CustomMarker  $custommarker
     * @return boolean
     */
    public function edit(User $user, CustomMarker $custommarker)
    {
        return $user->id === $custommarker->user_id;
    }



    /**
     * Determine whether the user can list markers.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function index(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can list scenes.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function admin_index(User $user)
    {
        return $user->role === 'admin';
    }



    /**
     * Determine whether the user can delete the custom marker.
     *
     * @param  \App\User  $user
     * @param  \App\CustomMarker  $custommarker
     * @return mixed
     */
    public function delete(User $user, CustomMarker $custommarker)
    {
        return $user->id === $custommarker->user_id;
    }

}
