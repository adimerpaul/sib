<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Inventory;

class CategoryController extends Controller{
    public function index(){ return Category::all();}
    public function show(Category $category){
        $inventory = Inventory::where('category_id', $category->id)->get();
        $num = count($inventory);
        $code = str_pad($num+1, 5, '0', STR_PAD_LEFT);
        return "SIB-".$category->code.'-'.$code;
    }
    public function store(StoreCategoryRequest $request){
        $count = Category::count();
        $request->merge(['code' => '00'.$count + 1]);
        return Category::create($request->all());
    }
    public function update(UpdateCategoryRequest $request, Category $category){ $category->update($request->all()); return $category;}
    public function destroy(Category $category){ $category->delete(); return $category;}
}
