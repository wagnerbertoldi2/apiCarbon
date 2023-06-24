<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmissionSourceRequest;
use Illuminate\Http\Request;
use App\Models\EmissionSourceModel;

class EmissionSourceController extends Controller{
    public function set(EmissionSourceRequest $request){
        $emissionSource = new EmissionSourceModel();
        $emissionSource->Name = $request->Name;
        $emissionSource->EmissionFactorId = $request->EmissionFactorId;
        $emissionSource->PropertyId = $request->PropertyId;
        $emissionSource->PeriodId = $request->PeriodId;
        $emissionSource->save();

        return response()->json($emissionSource, 201);
    }

    public function get(Request $request){
        if($request->has('PropertyId')){
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName')
                ->where('emissionsource.PropertyId', $request->PropertyId)
                ->get();
            return response()->json($emissionSource, 200);
        } elseif($request->has('id')){
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName')
                ->where('emissionsource.id', $request->id)
                ->get();
            $emissionSource = EmissionSourceModel::find($request->id);
            return response()->json($emissionSource, 200);
        } else {
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName')
                ->get();
            return response()->json($emissionSource, 200);
        }
    }

    public function update(EmissionSourceRequest $request){
        $emissionSource = EmissionSourceModel::find($request->id);
        $emissionSource->Name = $request->Name;
        $emissionSource->EmissionFactorId = $request->EmissionFactorId;
        $emissionSource->PropertyId = $request->PropertyId;
        $emissionSource->PeriodId = $request->PeriodId;
        $emissionSource->save();

        return response()->json($emissionSource, 200);
    }
}
