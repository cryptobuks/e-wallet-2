<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionBalance extends Model
{
    use HasFactory;

    protected $table = 'transaction_balance';

     protected $fillable = [
        'user_id', 
        'balance_id', 
        'transaction_type',
        'transaction_action',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function balance()
    {
        return $this->belongsTo(UserBalance::class, 'balance_id');
    }
}
