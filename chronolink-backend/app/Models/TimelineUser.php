<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TimelineUser extends Model
{
    use HasUuids;

    protected $table = 'timeline_user';

    protected $fillable = [
        'timeline_id',
        'user_id',
        'is_owner',
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
}
