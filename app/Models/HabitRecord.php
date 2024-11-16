<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitRecord extends Model
{
    use HasFactory;

    protected $fillable = ['habit_id', 'date', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }
}
