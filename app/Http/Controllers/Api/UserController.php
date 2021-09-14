<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        return response()->json(['message' => 'Success!'], 200);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'npp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return response()->json(['message' => 'Succes!'], 200);
    }

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $token =  $user->createToken('auth')->accessToken;
            $models = User::find($user['id']);
            $models->api_token = $token;
            $models->save();
            return response()->json(['token' => $token], 200);
        }
        return response()->json(['error'=>'Unauthorised'], 401);
    }

    public function logout(Request $request)
    {
        if($request->user()->token()->revoke()) {
            $models = User::find(Auth::user()['id']);
            $models->api_token = null;
            $models->save();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }
        return response()->json(['message' => 'Failed to log out!'], 500);
    }

    public function details()
    {
        return response()->json(['user' => Auth::user()], 200);
    }
}
