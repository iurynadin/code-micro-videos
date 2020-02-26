<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean'
    ];

    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
    
    // public function index()
    // {
    //     return Category::all();
    // }
    
    // public function store(Request $request)
    // {
    //     $this->validate($request, array_merge($this->rules, [
    //         'id' => 'uuid'
    //     ]));
    //     $category = Category::create($request->all());
    //     $category->refresh();
    //     return $category;
    // }

    
    // public function show(Category $category)
    // {
    //     return $category;
    // }

    
    // public function update(Request $request, Category $category)
    // {
    //     $this->validate($request, $this->rules);
    //     $category->update($request->all());
    //     return $category;
    // }

    
    // public function destroy(Category $category)
    // {
    //     $category->delete();
    //     return response()->noContent(); //204
    // }
}