<?php

namespace App\Http\Controllers\Admin\Concerns;

trait EnsuresAdminAccess
{
    private function ensureAdminAccess(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
