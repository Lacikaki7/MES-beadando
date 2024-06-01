<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status', 'current_location', 'target_location', 'estimated_completion_time', 'history'
    ];

    protected $casts = [
        'history' => 'array',
        'estimated_completion_time' => 'datetime',
    ];
}
