<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::paginate(Category::PAGINATION_COUNT);
        return view('admin.categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(Category::$rules);
        $imageUrl = $request->file("image")->store("categories", ["disk" => "public"]);
        
        $category = new Category();
        $category->fill($request->post());
        $category['image'] = $imageUrl;
        $category->save();

        return redirect('/admin/categories')->with("success", "The category created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the name only, because there is already an image to the category.
        $request->validate(["name" => "required|alpha_num"]);
        $category = Category::findOrFail($id);
        $category->fill($request->post());

        // If the user doesn't provide an image, keep the original one.
        if ($request->file("image")) {
            $imageUrl = $request->file("image")->store(["disk" => "public"]);
            $category['image'] = $imageUrl;
        }

        $category->save();
        return redirect('/admin/categories')->with("success", "The category edited successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // delete image of the category
        Storage::disk('public')->delete($category['image']);
        
        Category::destroy($id);
        return redirect('/admin/categories')->with('success', 'The category deleted successfully');
    }
}