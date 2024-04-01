<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodRequest;
use Illuminate\Http\Request;
use App\Models\PeriodModel;

class PeriodController extends Controller{
    public function set(PeriodRequest $request){
        $period = new PeriodModel();
        $period->Name = $request->Name;
        $period->InternalName = $request->InternalName;
        $period->save();
        return response()->json($period, 201);
    }

    public function get(Request $request){
        if($request->has('id')){
            $period = PeriodModel::find($request->id);
            return response()->json($period, 200);
        } else {
            $period = PeriodModel::all();
            return response()->json($period, 200);
        }
    }

    public function update(PeriodRequest $request){
        $period = PeriodModel::find($request->id);
        $period->Name = $request->Name;
        $period->InternalName = $request->InternalName;
        $period->save();
        return response()->json($period, 200);
    }
}
