<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AiVisibilityReport extends Model {
    use HasUuids;
    protected $fillable = ['brand_id','ai_system','query','response_text','brand_mentioned','mention_position','sentiment','topics','report_date'];
    protected $casts = ['brand_mentioned'=>'boolean','topics'=>'array','report_date'=>'date'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
}
