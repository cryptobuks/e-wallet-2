<?php

namespace App\Services;

use App\Models\TransactionBalance;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class TopUserTransactionService extends BaseService
{
	public $rules = [];

	public $ruleMessages = [];

	public function execute()
    {
    	$topTransactions = TransactionBalance::join('user_balance', 'transaction_balance.balance_id', '=', 'user_balance.id')
    				->join('users', 'user_balance.user_id', '=', 'users.id')
    				->select('users.username AS username',DB::raw('SUM(transaction_balance.amount) AS total_amount'))
    				->where('transaction_balance.transaction_action','transfer')
    				->where('transaction_balance.transaction_type','debit')
    				->orderBy('total_amount','desc')
    				->limit(10)
    				->groupBy('users.id')
    				->get();
    				
    	$transaction = [];
    	foreach ($topTransactions as $top) {
    		$transaction[] = [
    			"username" => $top->username,
    			"amount" => intval($top->total_amount),
    		];
    	}

    	$this->data = $transaction;
    }
}