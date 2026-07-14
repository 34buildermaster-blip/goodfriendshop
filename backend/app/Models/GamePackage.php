<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'game_id',
    'name',
    'sku',
    'description',
    'price',
    'cost',
    'currency',
    'required_fields',
    'status',
    'sort_order',
])]
class GamePackage extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_INACTIVE = 'inactive';

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function profit(): float
    {
        return (float) $this->price - (float) ($this->cost ?? 0);
    }

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'price' => 'decimal:2',
            'required_fields' => 'array',
            'sort_order' => 'integer',
        ];
    }
}
