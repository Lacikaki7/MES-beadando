<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'current_task'
    ];

    protected $casts = [
        'current_task' => 'array',
    ];
}