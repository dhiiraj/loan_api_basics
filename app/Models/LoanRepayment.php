<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;
    protected $table = 'loan_repayments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lan_number',
        'amount',
    ];

    private $error = false;

    public function calculate($loanAmount, $interest, $numOfweeks)
    {

        $errorArray = array();
        $weeklyPayment = 0;
        $inst = 10;

        $errorArray[0] = $this->checkWholeNum($loanAmount);
        $errorArray[1] = $this->checkInterest($interest);
        $errorArray[2] = $this->checkWholeNum($numOfweeks);

        if (!$this->getError()) {
            $rate = $interest / 5200;
            $rate = round($rate, 7);

            $weeklyPayment = ($rate + $rate / (pow($rate + 1, $numOfweeks) - 1)) * $loanAmount;
            $weeklyPayment = round($weeklyPayment, 2);
            return $weeklyPayment;
        } else {
            return false;
        }
    }

    public function checkWholeNum($string)
    {
        $pattern = '/^[1-9][0-9]*$/';
        if (!preg_match($pattern, $string)) {
            $this->setError();
            return false;
        } else {
            return true;
        }
    }

    public function checkInterest($string)
    {
        $pattern = '/^(?:[0]?\.[1-9]|[1-9]+[0-9]*\.[0-9])[0-9]*$|^[1-9][0-9]*$/';
        if (!preg_match($pattern, $string)) {
            $this->setError();
            return false;
        } else {
            return true;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError()
    {
        $this->error = true;
    }
}
