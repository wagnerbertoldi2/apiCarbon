<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect("https://cadastro.prisma.com.br");
        }

        // Verifica se o idprofile do usuário é 1
        if (Auth::user()->idprofile != 1) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
