<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_date',
        'due_date',
        'return_date',
        'fine_amount',
        'status',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'user_id');
    }
}
