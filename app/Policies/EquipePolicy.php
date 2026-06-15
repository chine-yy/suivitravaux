<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipe;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view-equipes');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Auth\Access\Response
     */
    public function view(User $user, Equipe $equipe)
    {
        return $user->hasPermission('view-equipes') || $equipe->users->contains('id', $user->id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-equipes');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Equipe $equipe)
    {
        return $user->hasPermission('edit-equipes') || $equipe->users->contains('id', $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Equipe $equipe)
    {
        return $user->hasPermission('delete-equipes');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Auth\Access\Response
     */
    public function restore(User $user, Equipe $equipe)
    {
        return $user->hasPermission('restaurer-equipes');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Auth\Access\Response
     */
    public function forceDelete(User $user, Equipe $equipe)
    {
        return $user->hasPermission('delete-equipes');
    }
}
