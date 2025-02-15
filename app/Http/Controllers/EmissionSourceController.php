<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmissionSourceRequest;
use App\Models\EmissionFactorModel;
use Illuminate\Http\Request;
use App\Models\EmissionSourceModel;

class EmissionSourceController extends Controller{
    public function set(EmissionSourceRequest $request){
        $periodId= EmissionFactorModel::find($request->EmissionFactorId)->PeriodId;

        $emissionSource = new EmissionSourceModel();
        $emissionSource->Name = $request->Name;
        $emissionSource->EmissionFactorId = $request->EmissionFactorId;
        $emissionSource->PropertyId = $request->PropertyId;
        $emissionSource->PeriodId = $periodId;
        $emissionSource->save();

        return response()->json($emissionSource, 201);
    }

    public function get(Request $request){
        if($request->has('PropertyId') && $request->has('id')){
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->join('period', 'emissionsource.PeriodId', '=', 'period.id')
                ->join('unit', 'emissionfactor.UnitId', '=', 'unit.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName', 'emissionfactor.Icon_react as icon', 'period.Name as Period', 'unit.InternalName as unit', 'unit.Name as unitName', 'emissionfactor.text')
                ->where('emissionsource.PropertyId', $request->PropertyId)
                ->where('emissionsource.id', $request->id)
                ->get();
            return response()->json(["oi",$emissionSource], 200);
        } elseif($request->has('PropertyId')){
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->join('unit', 'emissionfactor.UnitId', '=', 'unit.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName', 'emissionfactor.Icon_react as icon', 'unit.InternalName as unit', 'unit.Name as unitName', 'emissionfactor.text')
                ->where('emissionsource.PropertyId', $request->PropertyId)
                ->get();
            return response()->json($emissionSource, 200);
        } elseif($request->has('id')){
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->join('period', 'emissionsource.PeriodId', '=', 'period.id')
                ->join('unit', 'emissionfactor.UnitId', '=', 'unit.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName', 'emissionfactor.Icon_react as icon', 'period.Name as Period', 'unit.InternalName as unit', 'unit.Name as unitName', 'emissionfactor.text')
                ->where('emissionsource.id', $request->id)
                ->get();
            return response()->json($emissionSource, 200);
        } else {
            $emissionSource= EmissionSourceModel::join('emissionfactor', 'emissionsource.EmissionFactorId', '=', 'emissionfactor.id')
                ->join('unit', 'emissionfactor.UnitId', '=', 'unit.id')
                ->select('emissionsource.*', 'emissionfactor.Name as EmissionFactorName', 'emissionfactor.Icon_react as icon', 'unit.InternalName as unit', 'unit.Name as unitName', 'emissionfactor.text')
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
