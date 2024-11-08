<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelinePermission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'timeline_permissions';

    protected $fillable = [
        'user_timeline_id',
        'permission_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function userTimeline()
    {
        return $this->belongsTo(UserTimeline::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
