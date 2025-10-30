<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * TareaPolicy
 * 
 * Authorization policy for Task (Tarea) operations.
 * Ensures users can only manage their own tasks.
 */
class TareaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Tarea $tarea
     * @return Response|bool
     */
    public function view(User $user, Tarea $tarea): Response|bool
    {
        return $user->id === $tarea->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tarea $tarea
     * @return Response|bool
     */
    public function update(User $user, Tarea $tarea): Response|bool
    {
        return $user->id === $tarea->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tarea $tarea
     * @return Response|bool
     */
    public function delete(User $user, Tarea $tarea): Response|bool
    {
        return $user->id === $tarea->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tarea $tarea
     * @return Response|bool
     */
    public function restore(User $user, Tarea $tarea): Response|bool
    {
        return $user->id === $tarea->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tarea $tarea
     * @return Response|bool
     */
    public function forceDelete(User $user, Tarea $tarea): Response|bool
    {
        return $user->id === $tarea->user_id;
    }
}
