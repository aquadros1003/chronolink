<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    /** @use HasFactory<\Database\Factories\LabelFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'color',
        'timeline_id',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'timeline_id',
    ];
}
