<?php

namespace App\Policies;

use App\Models\AsignacionLocker;
use App\Models\User;

class AsignacionLockerPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, AsignacionLocker $asignacion): bool
    {
        return $asignacion->estado === 'activa'
            && $asignacion->usuario_id === $user->id;
    }

    public function update(User $user, AsignacionLocker $asignacion): bool
    {
        return false;
    }

    public function delete(User $user, AsignacionLocker $asignacion): bool
    {
        return false;
    }
}
