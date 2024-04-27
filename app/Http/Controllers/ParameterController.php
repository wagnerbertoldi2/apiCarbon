<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller{
    public function getParameter($paramter) {
        $parameter = Parameter::where("parameter", $paramter)->first();
        return response()->json($parameter, 200);
    }
}
