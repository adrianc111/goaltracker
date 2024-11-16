<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'goal_id', 'content', 'week_reference'];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope());

        static::creating(function ($note) {
            $note->user_id = auth()->id();
        });
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    // Scope to filter notes by week
    public function scopeForWeek($query, $week)
    {
        return $query->where('week_reference', $week);
    }
}
