<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Files\UserFile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserFilePolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        if( $user->hasAccess(['global-userfile']) )
            return true;
        else
            return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Files\UserFile  $model
     * @return mixed
     */
    public function view(User $user, UserFile $model)
    {
        if($user->hasAccess(['global-userfile']) || $user->id == $model->user_id)
            return true;
        else
            return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Files\UserFile  $model
     * @return mixed
     */
    public function update(User $user, UserFile $model)
    {
        if($user->hasAccess(['global-userfile']) || $user->id == $model->user_id)
            return true;
        else
            return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Files\UserFile  $model
     * @return mixed
     */
    public function delete(User $user, UserFile $model)
    {
        if($user->hasAccess(['global-userfile']) || $user->id == $model->user_id)
            return true;
        else
            return false;
    }
}
