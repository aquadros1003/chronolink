<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasUuids;

    protected $appends = ['label'];

    protected $fillable = [
        'title',
        'start_date',
        'location',
        'description',
        'end_date',
        'timeline_id',
        'user_id',
        'label_id',
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'timeline_id',
        'user_id',
        'label_id',
    ];

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function getLabelAttribute()
    {
        return $this->label()->first();
    }
}
