<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'year',
        'isbn',
        'description',
        'cover_image',
        'quantity',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }
}
