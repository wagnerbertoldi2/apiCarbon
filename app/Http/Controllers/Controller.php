<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function enviaEmail(){
        $emailController = new EmailController();
        $emailController->enviarEmail();
    }
}

