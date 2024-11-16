<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'goal_id', 'parent_id', 'title', 'completed', 'due_date', 'order', 'week_reference'];

    protected $casts = [
        'completed' => 'boolean',
        'due_date'  => 'datetime:Y-m-d',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope());

        static::creating(function ($item) {
            $item->user_id = Auth::id();
        });
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function setTitleAttribute($value)
    {
        // Trim the title
        $value = trim($value);
    
        // Check if the title has already been parsed (contains an anchor tag)
        if (strpos($value, '<a') === false) {
            // Regular expression to detect URLs
            $pattern = '/(https?:\/\/[^\s]+)/';
    
            // Replace URLs with anchor tags
            $value = preg_replace($pattern, '<a class="underline" href="$1" target="_blank">$1</a>', $value);
        }
    
        $this->attributes['title'] = $value;
    }    
    
}
