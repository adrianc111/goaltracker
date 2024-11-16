<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope());

        static::creating(function ($item) {
            $item->user_id = auth()->id();
        });
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst(strtolower(trim($value)));
    }
}
