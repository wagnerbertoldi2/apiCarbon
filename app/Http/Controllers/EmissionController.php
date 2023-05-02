<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmissionModel;
class EmissionController extends Controller{
    public function set(Request $request){
        $file = $request->file('attachment');
        $path = $file->store('attachments');

        $emission = new EmissionModel();
        $emission->Attachment = $file->getClientOriginalName();
        $emission->Amount = $request->Amount;
        $emission->InitialPeriod = $request->InitialPeriod;
        $emission->FinalPeriod = $request->FinalPeriod;
        $emission->EmissionSourceId = $request->EmissionSourceId;
        $emission->save();
        return response()->json($emission, 201);
    }

    public function get(Request $request){
        if($request->has('EmissionSourceId')){
            $emission = EmissionModel::where('EmissionSourceId', $request->EmissionSourceId)->get();
            return response()->json($emission, 200);
        } elseif($request->has('id')){
            $emission = EmissionModel::find($request->id);
            return response()->json($emission, 200);
        } else {
            $emission = EmissionModel::all();
            return response()->json($emission, 200);
        }
    }

    public function update(Request $request){
        $emission = EmissionModel::find($request->id);

        if (!is_null($request->Attachment)) {
            $emission->Attachment = $request->Attachment;
        }

        if (!is_null($request->Amount)) {
            $emission->Amount = $request->Amount;
        }

        if (!is_null($request->InitialPeriod)) {
            $emission->InitialPeriod = $request->InitialPeriod;
        }

        if (!is_null($request->FinalPeriod)) {
            $emission->FinalPeriod = $request->FinalPeriod;
        }

        if (!is_null($request->EmissionSourceId)) {
            $emission->EmissionSourceId = $request->EmissionSourceId;
        }

        $emission->save();
        return response()->json($emission, 200);
    }
}
