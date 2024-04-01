<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Models\CategoryModel;

class CategoryController extends Controller{
    public function set(CategoryRequest $request){
        $category = new CategoryModel();
        $category->Name = $request->Name;
        $category->save();

        return response()->json(['status' => 'success', 'data' => $category], 201);
    }

    public function get(Request $request){
        if($request->id == null){
            $category = CategoryModel::all();
        } else {
            $category = CategoryModel::find($request->id);
        }

        return response()->json(['status' => 'success', 'data' => $category], 200);
    }

    public function update(CategoryRequest $request){
        $category = CategoryModel::find($request->id);
        $category->Name = $request->Name;
        $category->save();

        return response()->json(['status' => 'success', 'data' => $category], 200);
    }
}
