<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    public function getParameter($parameter) {
        $value = Parameter::where('parameter', $parameter)->value('value');
        if ($value === null) {
            return response()->json(['error:' => 'Nenhum parametro encontrado'], 404);
        }
        return response()->json(['value' => $value]);
    }
}
