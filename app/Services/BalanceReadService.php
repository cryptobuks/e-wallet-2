<?php

namespace App\Services;

use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class BalanceReadService extends BaseService
{
	public $rules = [
		'userId' => 'required|numeric',
	];

	public $ruleMessages = [];

	public function execute()
    {
    	$balance = UserBalance::where('user_id',$this->request['userId'])->first();
        $balanceAmount = (!empty($balance)) ?  $balance->amount : 0;

        $this->data = [
            "balance" => $balanceAmount
        ];
    }
}