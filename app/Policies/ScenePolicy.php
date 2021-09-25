<?php

namespace App\Policies;

use App\User;
use App\Scene;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScenePolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Determine only admin can list all scenes.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function admin_index(User $user)
    {
        return $user->role === 'admin';
    }



    /**
     * Determine whether the user can list scenes.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function index(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can create scenes.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can view the scene.
     *
     * @param  \App\User  $user
     * @param  \App\Scene  $scene
     * @return boolean
     */
    public function view(User $user, Scene $scene)
    {
        return $user->id === $scene->user_id;
    }



    /**
     * Determine whether the user can edit the scene.
     *
     * @param  \App\User  $user
     * @param  \App\Scene  $scene
     * @return boolean
     */
    public function _edit(User $user, Scene $scene)
    {
        return $user->id === $scene->user_id;
    }



    /**
     * Determine whether the user can add scenes.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function add(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can update scenes.
     *
     * @param  \App\User  $user
     * @param  \App\Scene  $scene
     * @return boolean
     */
    public function edit(User $user, Scene $scene)
    {
        $owner = $user->id === $scene->user_id;
        $isEditable = $scene->editable;
        $notArchived = $scene->status !== 2;
        return $owner && $isEditable && $notArchived;
    }



    /**
     * Determine whether the user can delete the scene.
     *
     * @param  \App\User  $user
     * @param  \App\Scene  $scene
     * @return mixed
     */
    public function delete(User $user, Scene $scene)
    {
        $owner = $user->id === $scene->user_id;
        $isEditable = $scene->editable;
        $notArchived = $scene->status !== 2;
        return $owner && $isEditable && $notArchived;
    }


}
