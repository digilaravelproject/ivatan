<?php

namespace App\Policies;

use App\Models\User;

class HistoryPolicy
{
    public function viewHistory(User $admin): bool
    {
        return $admin->hasRole('admin');
    }
}
