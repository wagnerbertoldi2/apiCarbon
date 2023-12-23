<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use Illuminate\Http\Request;
use App\Models\PropertyModel;

class PropertyController extends Controller{
    public function set(PropertyRequest $request){
        $property = new PropertyModel();
        $property->Name = $request->Name;
        $property->Registration = $request->Registration;
        $property->CEP = $request->CEP;
        $property->City = $request->City;
        $property->Number = $request->Number;
        $property->Complement = $request->Complement;
        $property->NumberOfPeoples = $request->NumberOfPeoples;
        $property->Address = $request->Address;
        $property->UF = $request->UF;
        $property->UserId = $request->UserId;
        $property->CategoryId = $request->CategoryId;
        $property->save();
        return response()->json($property, 201);
    }

    public function get(Request $request){
        if(isset($request->id)){
            $property = PropertyModel::join('category', 'property.CategoryId', '=', 'category.id')
                ->select('property.*', 'category.name as categoryName')
                ->where('property.id', '=', $request->id)
                ->get();
            return response()->json($property, 200);
        } else {
            $property = PropertyModel::join('category', 'property.CategoryId', '=', 'category.id')
                ->select('property.*', 'category.name as categoryName')
                ->get();

            return response()->json($property, 200);
        }
    }

    public function update(PropertyRequest $request){
        $property = PropertyModel::find($request->id);
        $property->Name = $request->Name;
        $property->Registration = $request->Registration;
        $property->CEP = $request->CEP;
        $property->City = $request->City;
        $property->Number = $request->Number;
        $property->Complement = $request->Complement;
        $property->NumberOfPeoples = $request->NumberOfPeoples;
        $property->Address = $request->Address;
        $property->UF = $request->UF;
        $property->UserId = $request->UserId;
        $property->CategoryId = $request->CategoryId;
        $property->save();
        return response()->json($property, 200);
    }
}
