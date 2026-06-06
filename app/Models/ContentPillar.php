<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ContentPillar extends Model {
    use HasUuids;
    protected $fillable = ['brand_id','name','goal','color','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
