<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'frequency',
        'order',
        'group',
        'archived',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'archived'   => 'boolean',
        'start_date' => 'datetime:Y-m-d',
        'end_date'   => 'datetime:Y-m-d',
    ];

    public function records()
    {
        return $this->hasMany(HabitRecord::class);
    }

    public function scopePublic($query)
    {
        $query->where('archived', false);
    }

    public function scopeArchived($query)
    {
        $query->where('archived', true);
    }
}
