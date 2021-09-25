<?php

namespace App\Policies;

use App\User;
use App\Marker;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarkerPolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Determine whether the user can view the marker.
     *
     * @param  \App\User  $user
     * @param  \App\Marker  $marker
     * @return boolean
     */
    public function view(User $user, Marker $marker)
    {
        return $user->id === $marker->scene->user_id;
    }



    /**
     * Determine whether the user can create markers.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function add(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can update markers.
     *
     * @param  \App\User  $user
     * @param  \App\Marker  $marker
     * @return boolean
     */
    public function edit(User $user, Marker $marker)
    {
        $owner = $user->id === $marker->scene->user_id;
        $sceneIsEditable = $marker->scene->editable;
        $sceneNotArchived = $marker->scene->status !== 2;
        return $owner && $sceneIsEditable && $sceneNotArchived;
    }



    /**
     * Determine whether the user can download marker.
     *
     * @param  \App\User  $user
     * @param  \App\Marker  $marker
     * @return boolean
     */
    public function download(User $user, Marker $marker)
    {
        $owner = $user->id === $marker->scene->user_id;
        $sceneNotArchived = $marker->scene->status !== 2;
        return $owner && $sceneNotArchived;
    }



    /**
     * Determine whether the user can delete the marker.
     *
     * @param  \App\User  $user
     * @param  \App\Marker  $marker
     * @return mixed
     */
    public function delete(User $user, Marker $marker)
    {
        $owner = $user->id === $marker->scene->user_id;
        $sceneIsEditable = $marker->scene->editable;
        $sceneNotArchived = $marker->scene->status !== 2;
        return $owner && $sceneIsEditable && $sceneNotArchived;
    }


}
