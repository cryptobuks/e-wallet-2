<?php

namespace App\Services;

use App\Models\UserBalance;
use App\Models\User;
use App\Models\TransactionBalance;
use Illuminate\Support\Facades\DB;

class BalanceTransferService extends BaseService
{
	public $errorCode = 400;
	protected $sourceBalance;
	protected $destBalance;

	public $rules = [
		'userId' => 'required|numeric',
		'to_username' => 'required|string',
		'amount' => 'required|integer'
	];

	public $ruleMessages = [];

	public function execute()
    {
    	DB::beginTransaction();
    	try {
    		$this->getSourceBalance();
    		$this->getDestinationBalance();
    		if (!empty($this->errorMessage)) {
    			return true;
    		}
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
    	$transaction->balance_id = $this->sourceBalance->id;
    	$transaction->transaction_type = 'debit';
    	$transaction->transaction_action = 'transfer';
    	$transaction->amount = $this->request['amount'];

    	if ($transaction->save()) {
    		$deductBalance = $this->sourceBalance;
    		$deductBalance->amount = $this->sourceBalance->amount - $transaction->amount;
    		$deductBalance->save();
    	}

    	$transaction2 = new TransactionBalance;
    	$transaction2->user_id = $this->request['userId'];
    	$transaction2->balance_id = $this->destBalance->id;
    	$transaction2->transaction_type = 'credit';
    	$transaction2->transaction_action = 'transfer';
    	$transaction2->amount = $this->request['amount'];

    	if ($transaction2->save()) {
    		$updateBalance = $this->destBalance;
    		$updateBalance->amount = $this->destBalance->amount + $transaction->amount;
    		$updateBalance->save();
    	}

    	return $this->data = $updateBalance;
    }

    private function getBalance($userId)
    {
    	return UserBalance::where('user_id',$userId)->first();
    }

    private function getSourceBalance()
    {
    	$this->sourceBalance = $this->getBalance($this->request['userId']);

    	if ($this->sourceBalance->amount < $this->request['amount']) {
    		$this->errorMessage = "Insufficient balance";
    		$this->errorCode = 400;
    		return false;
    	}
    }

    private function getDestinationBalance()
    {
    	$user = User::where('username',$this->request['to_username'])->first();

    	if (empty($user)) {
    		$this->errorMessage = "Destination user not found";
    		$this->errorCode = 404;
    		return false;
    	}

    	$this->destBalance = $this->getBalance($user->id);
    }
}