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
            $emissionSource= EmissionSourceModel::join('EmissionFactor', 'EmissionSource.EmissionFactorId', '=', 'EmissionFactor.id')
                ->select('EmissionSource.*', 'EmissionFactor.Name as EmissionFactorName')
                ->where('EmissionSource.PropertyId', $request->PropertyId)
                ->get();
            return response()->json($emissionSource, 200);
        } elseif($request->has('id')){
            $emissionSource= EmissionSourceModel::join('EmissionFactor', 'EmissionSource.EmissionFactorId', '=', 'EmissionFactor.id')
                ->select('EmissionSource.*', 'EmissionFactor.Name as EmissionFactorName')
                ->where('EmissionSource.id', $request->id)
                ->get();
            $emissionSource = EmissionSourceModel::find($request->id);
            return response()->json($emissionSource, 200);
        } else {
            $emissionSource= EmissionSourceModel::join('EmissionFactor', 'EmissionSource.EmissionFactorId', '=', 'EmissionFactor.id')
                ->select('EmissionSource.*', 'EmissionFactor.Name as EmissionFactorName')
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
