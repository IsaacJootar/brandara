<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PlatformConnection extends Model {
    use HasUuids;
    protected $fillable = ['brand_id','platform','platform_user_id','platform_username','access_token','refresh_token','token_expires_at','status','last_posted_at','follower_count'];
    protected $casts = ['token_expires_at'=>'datetime','last_posted_at'=>'datetime'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
