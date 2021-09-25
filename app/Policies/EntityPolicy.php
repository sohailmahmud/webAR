<?php

namespace App\Policies;

use App\User;
use App\Entity;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;


    public function before($user, $ability)
    {
       if ($user->role === 'admin') {
            return true;
        }
    }



    /**
     * Determine whether the user can view entities.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function admin_index(User $user)
    {
        return false;
    }



    /**
     * Determine whether the user can view the entity.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return boolean
     */
    public function view(User $user, Entity $entity)
    {
        return $user->id === $entity->marker->scene->user_id;
    }



    /**
     * Determine whether the user can create entities.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function add(User $user)
    {
        return $user->role === 'editor';
    }



    /**
     * Determine whether the user can update entities.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return boolean
     */
    public function edit(User $user, Entity $entity)
    {
        $owner = $user->id === $entity->marker->scene->user_id;
        $sceneIsEditable = $entity->marker->scene->editable;
        $sceneNotArchived = $entity->marker->scene->status !== 2;
        return $owner && $sceneIsEditable && $sceneNotArchived;
    }



    /**
     * Determine whether the user can delete the entity.
     *
     * @param  \App\User  $user
     * @param  \App\Entity  $entity
     * @return mixed
     */
    public function delete(User $user, Entity $entity)
    {
        $owner = $user->id === $entity->marker->scene->user_id;
        $sceneIsEditable = $entity->marker->scene->editable;
        $sceneNotArchived = $entity->marker->scene->status !== 2;
        return $owner && $sceneIsEditable && $sceneNotArchived;
    }


}
