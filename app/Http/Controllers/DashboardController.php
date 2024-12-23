<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller{
    public function index(){
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $idusuario = $user->id;

        $dados= DB::table('dashboard_por_usuario')->where('idusuario',$idusuario)->get();

        return response()->json($dados, 201);
    }
}
