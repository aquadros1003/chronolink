<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory, HasUuids;

    protected $appends = ['is_owner'];

    protected $fillable = [
        'title',
        'description',
        'owner_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
        'owner_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_timeline', 'timeline_id', 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function userTimelines()
    {
        return $this->hasMany(UserTimeline::class);
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function getIsOwnerAttribute()
    {
        return $this->owner_id === auth()->id();
    }
}
