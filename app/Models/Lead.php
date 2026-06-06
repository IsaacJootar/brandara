<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Lead extends Model {
    use HasUuids;
    protected $fillable = ['brand_id','platform','platform_user_id','name','headline','company','profile_url','tag','notes','follow_up_at','total_engagements','last_engaged_at'];
    protected $casts = ['follow_up_at'=>'date','last_engaged_at'=>'datetime'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
