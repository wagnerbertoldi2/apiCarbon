<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassResetRequest;
use App\Http\Requests\UserRequest;
use App\Models\codeModel;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CodigoNumericoEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function verificarSenha(Request $request){
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Senha correta', "value" => 1], 200);
        } else {
            return response()->json(['message' => 'Senha incorreta', "value" => 0], 401);
        }
    }

    public function update(Request $request){
        $user = User::find($request['id']);
        $user->FirstName = $request['firstName'];
        $user->LastName = $request['lastName'];
        $user->CPF = $request['cpf'];
        $user->RG = $request['rg'];
        $user->CNPJ = $request['cnpj'];
        $user->email = $request['email'];

        if($request['password'] != '' || !empty($request['password'])) {
            $user->password = bcrypt($request['password']);
        }

        $user->save();

        return response()->json($user, 201);
    }

    public function getCodeEmail(Request $request){
        $email= $request['email'];

        $user = User::where('email', $email)->first();

        if($user == null){
            return response()->json(['msg'=>'error'], 400);
        } else {
            $iduser = $user->id;
            $code = rand(100000, 999999);

            $objCode= new codeModel();
            $objCode->code = $code;
            $objCode->iduser = $iduser;
            $objCode->save();

            Mail::to($email)->send(new CodigoNumericoEmail(['code' => $code]));

            return response()->json(['msg'=>'success'], 201);
        }
    }

    public function passReset(PassResetRequest $request){
        $user = User::where('email', $request->email)->first();
        if(!empty($user)){
            $code= DB::table("coderesetpassword")->select("code")->where("status",2)->where("iduser",$user->id)->orderBy("id","desc")->first();
        } else {
            $code= "";
        }

        if (empty($user) || $code->code !== $request->code) {
            return response()->json([
                'message' => 'Código ou e-mail inválido.'
            ], 401);
        } else {
            DB::table("users")->where("id",$user->id)->update([
                "password" => bcrypt($request->password)
            ]);

            DB::table("coderesetpassword")->where("code",$code->code)->update([
                "status" => 1
            ]);

            return response()->json([
                'message' => 'Senha redefinida com sucesso!'
            ]);
        }
    }

    public function SendEmailResetPassword(Request $request){
        $email= $request->email;
        $user= DB::table("users")->where("email",$email)->first();

        if(empty($user->email)){
            Mail::to($email)->send(new CodigoNumericoEmail($user));
        } else {
            Mail::to($user->email)->send(new CodigoNumericoEmail($user));
        }

        return true;
    }
}
