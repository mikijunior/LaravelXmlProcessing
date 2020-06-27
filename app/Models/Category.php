<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
