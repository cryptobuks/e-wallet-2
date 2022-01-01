<?php

namespace App\Services;

use App\Models\TransactionBalance;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class TopTransactionUserService extends BaseService
{
	public $rules = [
		'userId' => 'required|numeric',
	];

	public $ruleMessages = [];

	public function execute()
    {
    	$balance = $this->getBalance();
    	$topTransactions = TransactionBalance::where('balance_id',$balance->id)
    				->orderBy('amount','desc')
    				->limit(10)
    				->get();

    	$transaction = [];
    	foreach ($topTransactions as $top) {
    		$transaction[] = [
    			"amount" => ($top->transaction_type == 'debit') ? -$top->amount : intval($top->amount),
    			"username" => $top->user->username
    		];
    	}

    	$this->data = $transaction;
    }

    private function getBalance()
    {
    	return UserBalance::where('user_id',$this->request['userId'])->first();
    }
}