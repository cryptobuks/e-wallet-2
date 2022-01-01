<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\BalanceTopupService;
use App\Services\BalanceReadService;
use App\Services\BalanceTransferService;
use App\Services\TopTransactionUserService;
use App\Services\TopUserTransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
	public function __construct(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function balanceRead(Request $request)
    {
    	$service = new BalanceReadService([
    		"userId" => $this->user->id
    	]);
    	$service->run();
        if($service->isError()) {
        	return response()->json([
                'success' => false,
                'message' => $service->errorMessage,
           	], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $service->data,
        ], 200); 
    }

    public function topTransactionsUser(Request $request)
    {
    	$service = new TopTransactionUserService([
    		"userId" => $this->user->id
    	]);
    	$service->run();
        if($service->isError()) {
        	return response()->json([
                'success' => false,
                'message' => $service->errorMessage,
           	], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $service->data,
        ], 200); 
    }

    public function topUsersTransaction(Request $request)
    {
    	$service = new TopUserTransactionService();
    	$service->run();
        if($service->isError()) {
        	return response()->json([
                'success' => false,
                'message' => $service->errorMessage,
           	], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $service->data,
        ], 200); 
    }

    public function transfer(Request $request)
    {
    	$input = $request->all();
    	$params = [
    		"userId" => $this->user->id
    	];

    	$service = new BalanceTransferService(array_merge($input,$params));
    	$service->run();
        if($service->isError()) {
        	return response()->json([
                'success' => false,
                'message' => $service->errorMessage,
           	], $service->errorCode);
        }

        return response()->json([
            'success' => true,
            'message' => "Transfer successful",
        ], 204); 
    }

    public function balanceTopup(Request $request)
    {
    	$input = $request->all();
    	$params = [
    		"userId" => $this->user->id
    	];

    	$service = new BalanceTopupService(array_merge($input,$params));
    	$service->run();
        if($service->isError()) {
        	return response()->json([
                'success' => false,
                'message' => $service->errorMessage,
           	], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Topup successful",
        ], 204); 
    }
}