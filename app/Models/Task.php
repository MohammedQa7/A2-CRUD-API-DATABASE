<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id', 'created_at'])]
class Task extends Model
{
    protected $casts = [
        'done' => 'boolean',
    ];
}