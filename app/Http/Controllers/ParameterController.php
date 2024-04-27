<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    public function getParameter($id) {
        $parameter = Parameter::findOrFail($id);
        return response()->json($parameter, 200);
    }
}
