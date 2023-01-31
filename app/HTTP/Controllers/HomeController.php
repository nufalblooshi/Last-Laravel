<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\NewsLetter;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    //
    function index()
    {   
        $products = Product::all();
        return view('shop.index', compact('products'));
    }

    function subscribeToNewsletter(Request $request) {
        $request->validate([
            "email" => "required|email"
        ]);

        $newsLetter = new NewsLetter();
        $newsLetter->fill($request->post());
        $newsLetter->save();

        return redirect("/")->with("success", "you have subscribe to our newsletter");
    }
}