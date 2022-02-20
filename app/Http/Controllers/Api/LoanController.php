<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    protected $jwt;
    /**
     * Create a new LoanController instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->guard = "api";
    }

    /**
     * API to Apply for a loan.
     * @param @ammount @tenure
     * @respose json
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loanApply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:100',
            'tenure' => 'required|integer|between:1,360',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }
        $loan_account = 'LAN' . strtoupper(substr(md5(microtime()), -8));
        $loan = Loan::create(array_merge(
            $validator->validated(),
            [
                'user_id' => $request->logged_user_id,
                'lan_number' => $loan_account,
                'no_of_emi' => $request->tenure,
                'status' => 0, // by default loan is in active
            ]
        ));
        return $this->successResponse($loan, ['Loan applied successfully']);
    }

    /**
     * Make payment
     * @param @mount @lan_number(loan acount number)
     * @response json
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function makePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'lan_number' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $active_loan = Loan::where(['lan_number' => $request->lan_number])->get(['no_of_emi', 'status', 'emi_amount'])->first();
        if (isset($active_loan)) {
            /*If loan acount is active*/
            if ($active_loan->status == 1) {
                if ($active_loan->emi_amount != $request->amount) {
                    return $this->errorResponse(["Your payable EMI amount is " . $active_loan->emi_amount], 202);
                } else
                    /*To check if all emi alreayd paid off*/
                    if ($active_loan->no_of_emi == 0) {
                        return $this->errorResponse(CLOSED, 200);
                    } else {
                        $loan = LoanRepayment::create(array_merge(
                            $validator->validated()
                        ));
                        Loan::where('lan_number', $request->lan_number)
                            ->update(['no_of_emi' => $active_loan->no_of_emi - 1]);
                        if ($active_loan['no_of_emi'] == 1) {
                            Loan::where('lan_number', $request->lan_number)
                                ->update(['status' => 2]);
                        }
                        return $this->successResponse($request->amount, PAYMENT_SUCCESS);
                    }
            }
            /*Check if loan is closed*/
            if ($active_loan->status == 2) {
                return $this->errorResponse(CLOSED, 202);
            }
            //If there is no active loan record found in system
            return $this->errorResponse(NO_ACTIVE_RECORD, 400);
        }
        /* If there is no record in system */
        return $this->errorResponse(NOT_FOUND, 404);
    }

    /**
     * Approve/Close/Rreject loan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|between:0,2',
            'lan_number' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        /* Fetch the loan details */
        $loan_account = Loan::where(['lan_number' => $request->lan_number])->get(['amount', 'tenure', 'status'])->first();
        if ($loan_account) {
            /* Approve loan with emi amount (interest default 10% pa calculated weekly)  for new loan */
            if ($request->status == 1 && !isset($request->emi_amount)) {
                $calculations = new LoanRepayment();
                $emi = $calculations->calculate($loan_account->amount, 10, $loan_account->tenure);
                if ($emi) {
                    $response = Loan::where('lan_number', $request->lan_number)
                        ->update(['emi_amount' => $emi, 'status' => 1, 'no_of_emi' => $loan_account->tenure]);
                    return $this->successResponse(['emi_amount' => $emi, 'no_of_emi' => $loan_account->tenure], UPDATED);
                }
            } else {
                /* To update other status */
                $response = Loan::where('lan_number', $request->lan_number)
                    ->update(['status' => $request->status]);
                if ($response) {
                    return $this->successResponse(['status' => $request->status], UPDATED);
                }
            }
            //If failed to update status
            return $this->errorResponse(FAILED, 400);
        }
        /* If there is no record in system */
        return $this->errorResponse(NOT_FOUND, 404);
    }
}
