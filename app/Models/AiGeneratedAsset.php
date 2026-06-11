<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneratedAsset extends Model
{
    use HasUuids;

    protected $fillable = [
        'brand_id', 'type', 'content', 'status', 'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'json_ld' => 'JSON-LD Schema',
            'local_business_schema' => 'LocalBusiness Schema',
            'faq_schema' => 'FAQ Schema',
            'about_copy' => 'About Page Copy',
            'brand_markdown' => 'Brand Markdown File',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
