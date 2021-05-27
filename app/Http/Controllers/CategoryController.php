<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getSubCategory(Request $request){
        $sub_categories = \App\Models\ItemSubCategory::select('id', 'name')->where('category', '=', $request->input('category'))->orderBy('name')->get();
        
        return $sub_categories;
    }
}
