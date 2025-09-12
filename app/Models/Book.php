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

    public function availableQuantity()
    {
        $borrowed = $this->loanDetails()
            ->whereHas('loan', function ($q) {
                $q->where('status', 'borrowed');
            })
            ->sum('quantity');

        return $this->quantity - $borrowed;
    }

    public function loanDetails()
    {
        return $this->hasMany(LoanDetail::class, 'book_id');
    }
}
