<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'amount', 'tenure', 'status', 'no_of_emi', 'lan_number', 'emi_amount'
    ];

    /**
     * Get the repayments for the loan.
     */
    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}
