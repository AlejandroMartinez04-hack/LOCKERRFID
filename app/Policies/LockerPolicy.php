<?php

namespace App\Policies;

use App\Models\Locker;
use App\Models\User;

class LockerPolicy
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

    public function view(User $user, Locker $locker): bool
    {
        return $locker->asignacionActiva()
            ->where('usuario_id', $user->id)
            ->exists();
    }

    public function update(User $user, Locker $locker): bool
    {
        return false;
    }

    public function delete(User $user, Locker $locker): bool
    {
        return false;
    }
}
