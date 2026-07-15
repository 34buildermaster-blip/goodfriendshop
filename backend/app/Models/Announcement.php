<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['message', 'is_active', 'sort_order', 'starts_at', 'ends_at'])]
class Announcement extends Model
{
    public function isVisible(): bool
    {
        $now = now();

        return $this->is_active
            && (blank($this->starts_at) || $this->starts_at->lte($now))
            && (blank($this->ends_at) || $this->ends_at->gte($now));
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
