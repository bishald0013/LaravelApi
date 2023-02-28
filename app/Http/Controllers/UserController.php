<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed',
            'tc'=>'required'
        ]);

        if(User::where('email', $request->email) -> first()){
            return response([
                'message' => 'email already exist',
                'status' => 'failed'
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tc' => json_decode($request->tc),
        ]);

        $token = $user->createToken($request->email)->plainTextToken;

        return response([
            'token' => $token,
            'message' => 'Registration Success',
            'status' => 'success'
        ], 201);

    }

    //user login
    public function login(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if($user && Hash::check($request->password, $user->password)){
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                'token' => $token,
            'message' => 'login Success',
            'status' => 'success'
            ], 200);
        }

        return response([
            'message'=>'Email id or password not match',
            'status'=>'fails'
        ], 401);
        
    }

    //logout user
    public function logout(){
        auth()->user()->token()->token();
        
        return response([
            'message'=>'logout success',
            'status'=>'success'
        ]);
    }
}
