<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmissionFactorRequest;
use Illuminate\Http\Request;
use App\Models\EmissionFactorModel;
class emissionFactorController extends Controller{
    public function get(Request $request){
        if($request->has('id')){
            $emissionFactor = EmissionFactorModel::where('id', $request->id)->get();
            return response()->json($emissionFactor);
        } else {
            $emissionFactor = EmissionFactorModel::all();
            return response()->json($emissionFactor);
        }
    }

    public function set(EmissionFactorRequest $request){
        $emissionFactor = new EmissionFactorModel();
        $emissionFactor->Name = $request->Name;
        $emissionFactor->UnitId = $request->UnitId;
        $emissionFactor->save();
        return response()->json($emissionFactor);
    }

    public function update(EmissionFactorRequest $request){
        $emissionFactor = EmissionFactorModel::where('id', $request->id)->first();
        $emissionFactor->Name = $request->Name;
        $emissionFactor->UnitId = $request->UnitId;
        $emissionFactor->save();
        return response()->json($emissionFactor);
    }
}
