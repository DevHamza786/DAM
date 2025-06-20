<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ShareableLink extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'token',
        'asset_id',
        'created_by',
        'expires_at',
        'max_views',
        'view_count',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'max_views' => 'integer',
        'view_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(32);
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function views()
    {
        return $this->hasMany(ShareableLinkView::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['expires_at', 'max_views', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_views && $this->view_count >= $this->max_views) {
            return false;
        }

        return true;
    }

    public function getShareUrlAttribute()
    {
        return route('share.asset', $this->token);
    }

    public function getQrCodeUrlAttribute()
    {
        return route('share.qr', $this->token);
    }
} 