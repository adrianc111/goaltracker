<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'type', 'completed', 'due_date', 'year', 'order'];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime:Y-m-d',
    ];

    public static array $TYPES = [
        'personal' => [
            'id' => 'personal',
            'title' => 'Personal',
        ],
        'health' => [
            'id' => 'health',
            'title' => 'Health',
        ],
        'finances' => [
            'id' => 'finances',
            'title' => 'Finances / Work',
        ],
        'home_car' => [
            'id' => 'home_car',
            'title' => 'Home / Car',
        ],
        'relationships' => [
            'id' => 'relationships',
            'title' => 'Relationships',
        ],
        'hobbies_fun' => [
            'id' => 'hobbies_fun',
            'title' => 'Hobbies / Fun',
        ],
        'someday_maybe' => [
            'id' => 'someday_maybe',
            'title' => 'Someday / Maybe',
        ],
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope());

        static::creating(function ($goal) {
            $goal->user_id = auth()->id();
//            $goal->description = "<h2>Purposeful</h2><p>(What’s the main purpose of this goal? Why is it meaningful to you?)</p><p><br></p><p><br></p><p><br></p><h2>Actionable</h2><p>(Break the goal down into clear, actionable steps. What actions will help you achieve it?)</p><p><br></p><p><br></p><p><br></p><h2>Continuous</h2><p>Focus on creating habits or processes that are ongoing. How will this goal become part of your routine?</p><p><br></p><p><br></p><p><br></p><h2>Trackable</h2><p>How will you measure progress? What metrics or milestones will help you stay on track?</p><p><br></p><p><br></p><p><br></p>";

            // If due_date set, remove the year
            if($goal->due_date) {
                $goal->year = null;
            }
        });

        static::updating(function ($goal) {
            // If due_date set, remove the year
            if($goal->due_date) {
                $goal->year = null;
            }
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$TYPES[$this->type]['title'];
    }

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = trim($value);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where(function($query) use ($year) {
            $query->whereYear('due_date', $year)
                ->orWhere('year', $year);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('completed', false);
    }

}
