<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTimeline extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_timeline';

    protected $fillable = [
        'timeline_id',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissions()
    {
        return $this->BelongsToMany(Permission::class, 'timeline_permissions', 'user_timeline_id', 'permission_id');
    }
}
