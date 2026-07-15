<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'slug',
    'type',
    'excerpt',
    'content',
    'cover_image_path',
    'meta_title',
    'meta_description',
    'og_image_path',
    'published_at',
    'status',
    'sort_order',
])]
class ContentPost extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_ACTIVITY = 'activity';

    public const TYPE_NEWS = 'news';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public function coverImageUrl(): ?string
    {
        if (blank($this->cover_image_path)) {
            return null;
        }

        if (Str::startsWith($this->cover_image_path, ['http://', 'https://', '/'])) {
            return $this->cover_image_path;
        }

        return Storage::url($this->cover_image_path);
    }

    public function ogImageUrl(): ?string
    {
        if (blank($this->og_image_path)) {
            return $this->coverImageUrl();
        }

        if (Str::startsWith($this->og_image_path, ['http://', 'https://', '/'])) {
            return $this->og_image_path;
        }

        return Storage::url($this->og_image_path);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }
}
