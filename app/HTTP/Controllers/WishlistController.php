<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{

    function getWishList() {
        return Session::get('ids', []);
    }

    function getProductsfromWishList() {
        $wishList = $this->getWishList();
        return array_map(function ($id) {
            return Product::findOrFail($id);
        }, $wishList);
    }

    function index() {
        $likedProducts = $this->getProductsfromWishList();
        return view('shop.wishlist', ['products' => $likedProducts]);
    }

    function removeProductFromWishList(Request $request) {
        $id = $request->get('id');
        if (!$id) {
            return abort(404);
        }
        
        $ids = $this->getWishList();
        $indexOfid = array_search($id, $ids);

        if ($indexOfid === false) {
            return abort(404);
        }

        array_splice($ids, $indexOfid, 1);
        $wishListCount = count($ids);
        
        Session::put('ids', $ids);

        return response()->json(["id" => $id, "count" => $wishListCount]);
    }

    function addProductToWishList(Request $request)
    {
        if ($request->has('id')) {
            $ids = Session::get('ids', []);
            if (!in_array($request->get('id'), $ids)) {
                array_push($ids, $request->get('id'));
            }

            $likedListCount = count($ids);
            Session::put('ids', $ids);
            return response()->json(["count" => $likedListCount]);
        }

        return abort(404);
    }
}