<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use App\Models\UnitModel;
class UnitController extends Controller{
    public function set(UnitRequest $request){
        $unit = new UnitModel();
        $unit->Name = $request->Name;
        $unit->InternalName = $request->InternalName;
        $unit->save();
        return response()->json($unit, 201);
    }

    public function get(Request $request){
        if($request->id == null){
            $unit= UnitModel::find($request->id);
        } else {
            $unit = UnitModel::all();
        }

        return response()->json($unit, 200);
    }

    public function update(UnitRequest $request){
        $unit = UnitModel::find($request->id);
        $unit->Name = $request->Name;
        $unit->InternalName = $request->InternalName;
        $unit->save();
        return response()->json($unit, 200);
    }
}
