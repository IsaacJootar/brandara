<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MediaFile extends Model {
    use HasUuids;
    protected $fillable = ['brand_id','uploaded_by','filename','storage_path','mime_type','file_size_kb','width','height','alt_text','tags'];
    protected $casts = ['tags'=>'array'];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class,'uploaded_by'); }
}
