<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope());

        static::creating(function ($item) {
            $item->user_id = auth()->id();
        });
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strtolower(trim($value));
    }
}
