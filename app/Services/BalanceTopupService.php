<?php

namespace App\Services;

use App\Models\UserBalance;
use App\Models\TransactionBalance;
use Illuminate\Support\Facades\DB;

class BalanceTopupService extends BaseService
{
	protected $balance;

	public $rules = [
		'userId' => 'required|numeric',
		'amount' => 'required|numeric|max:9999999'
	];

	public $ruleMessages = [];

	public function execute()
    {
    	DB::beginTransaction();
    	try {
    		$this->getBalance();
    		$this->saveTransaction();
    		DB::commit();
    	} catch (\Exception $e) {
    		DB::rollback();
    		$this->errorMessage = 'transaction failed';
    	}

    }

    private function saveTransaction()
    {
    	$transaction = new TransactionBalance;
    	$transaction->user_id = $this->request['userId'];
    	$transaction->balance_id = $this->balance->id;
    	$transaction->transaction_type = 'credit';
    	$transaction->transaction_action = 'topup';
    	$transaction->amount = $this->request['amount'];

    	if ($transaction->save()) {
    		$updateBalance = $this->balance;
    		$updateBalance->amount = $this->balance->amount + $transaction->amount;
    		$updateBalance->save();
    	}

    	return $this->data = $updateBalance;
    }

    private function getBalance()
    {
    	$this->balance = UserBalance::where('user_id',$this->request['userId'])->first();
    }
}