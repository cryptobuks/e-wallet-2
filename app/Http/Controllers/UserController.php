<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('username');
        $validator = Validator::make($data, [
            'username' => 'required|string|unique:users'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
            ]);
            if ($user) {
                $this->createBalance($user);
                if (! $token = JWTAuth::fromUser($user)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login credentials are invalid.',
                    ], 400);
                }
            } 
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Login credentials are invalid.',
            ], 400);
        }

        //User created, return success response
        return response()->json([
            'success' => true,
            'token' => $token,
        ], 201);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username');

        //valid credential
        $validator = Validator::make($credentials, [
            'username' => 'required|string'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $user = User::where('username',$request->username)->first();

        if (!$user) {
            return response()->json([
                    'success' => false,
                    'message' => 'Username not found',
                ], 404);
        }
        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::fromUser($user)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function getUser(){
        $user = auth('api')->user();
        return response()->json(['user'=>$user], 201);
    }

    private function createBalance($user)
    {
        $balance = new UserBalance;
        $balance->user_id = $user->id;
        $balance->save();
    }
}