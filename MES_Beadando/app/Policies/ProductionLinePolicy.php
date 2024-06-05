<?php

namespace App\Policies;

use App\Models\ProductionLine;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductionLinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Production', 'Customer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductionLine $productionLine): bool
    {
        return $user->hasRole(['Admin', 'Production', 'Customer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create Production Lines');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductionLine $productionLine): bool
    {
        return $user->hasPermissionTo('Edit Production Lines');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductionLine $productionLine): bool
    {
        return $user->hasPermissionTo('Delete Production Lines');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductionLine $productionLine): bool
    {
        return $user->hasRole(['Admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductionLine $productionLine): bool
    {
        return $user->hasRole(['Admin']);
    }
}
