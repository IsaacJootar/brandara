<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Notification extends Model {
    use HasUuids;
    protected $fillable = ['user_id','brand_id','type','title','message','action_url','channels','read_at','sent_at'];
    protected $casts = ['channels'=>'array','read_at'=>'datetime','sent_at'=>'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
