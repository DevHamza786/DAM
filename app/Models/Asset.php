<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'original_filename',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'description',
        'metadata',
        'status',
        'uploaded_by',
        'category',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function shareableLinks()
    {
        return $this->hasMany(ShareableLink::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getFileUrlAttribute()
    {
        return storage_url($this->file_path);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? storage_url($this->thumbnail_path) : null;
    }

    public function getHumanReadableSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2) . ' ' . $units[$index];
    }
}
