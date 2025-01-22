<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        if ($user->idprofile == 1) {
            $idusuario = !empty($request->idusuario) ? $request->idusuario : $user->id;
        } else {
            $idusuario = $user->id;
        }

        $dados= DB::table('dashboard_por_usuario')->where('idusuario',$idusuario)->get();

        return response()->json($dados, 201);
    }

    public function dashboardListUsers(Request $request){
        $user = Auth::user();
        if($user->idprofile == 1) {
            $users= User::all();
            return response()->json($users, 201);
        } else {
            return response()->json([], 201);
        }
    }

    public function dashboardProfileUser(Request $request){
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $profile = $user->idprofile;
        return response()->json(["idprofile"=>$profile], 201);
    }
}
