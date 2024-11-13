<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Business;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver el negocio.
     */
    public function view(User $user, Business $business)
    {
        return $user->id === $business->owner_id;
    }

    /**
     * Determina si el usuario puede modificar el negocio.
     */
    public function update(User $user, Business $business)
    {
        // Solo puede modificar el negocio si es propietario
        return $user->id === $business->owner_id;
    }

    /**
     * Determina si el usuario puede eliminar el negocio.
     */
    public function delete(User $user, Business $business)
    {
        // Solo puede eliminar el negocio si es propietario
        return $user->id === $business->owner_id;
    }
}
