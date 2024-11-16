<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'completed'];

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
}
