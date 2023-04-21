<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class UserController extends Controller{

    public function me(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to authenticate token.'], 401);
        }

        unset($user->password);

        return response()->json(compact('user'), 200);
    }

    public function set(UserRequest $request){
        $user = new User();
        $user->FirstName = $request['firstName'];
        $user->LastName = $request['lastName'];
        $user->CPF = $request['cpf'];
        $user->RG = $request['rg'];
        $user->CNPJ = $request['cnpj'];
        $user->email = $request['email'];
        $user->password = bcrypt($request['password']);
        $user->save();

        return response()->json($user, 201);
    }

}
