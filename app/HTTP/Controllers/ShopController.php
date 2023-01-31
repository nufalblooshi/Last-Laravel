<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    function index(Request $request)
    {

        $inputs = $request->all();

        $query = Product::query();

        if (isset($inputs['keyword'])) {
            $query = $query->where('name', 'like', '%' . $inputs['keyword'] . '%');
        }

        if (isset($inputs['category_id'])) {
            $query = $query->where('category_id', $inputs['category_id']);
        }

        if (isset($inputs['color'])) {
            if (!in_array('-1', $inputs['color'])) {
                $query = $query->whereIn('color_id', $inputs['color']);
            }
        }

        if (isset($inputs['size'])) {
            if (!in_array('-1', $inputs['size'])) {
                $query = $query->whereIn('size_id', $inputs['size']);
            }
        }

        if (isset($inputs['price'])) {
            if (!in_array('-1', $inputs['price'])) {
                $query = $query->where(function ($q) use ($inputs) {
                    foreach ($inputs['price'] as $price) {
                        $q = $q->orWhereBetween('price', [$price, $price + 100]);
                    }
                });
            }
        }


        $products = $query->paginate(Product::PAGINATION_COUNT);
        $sizes = Size::all();
        $colors = Color::all();
        return view('shop.shop', compact('products', 'sizes', 'colors'));
    }
}