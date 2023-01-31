<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{

    use \App\Traits\CartSummaryTrait;

    function index()
    {

        $cartProducts = Session::get('cart', []);
        $products = $this->mapSessionProducts($cartProducts);
        $subTotal = $this->calculateSubTotal($cartProducts);
        $shipping = $this->calculateShipping($cartProducts);
        $total = $this->calculateTotal($cartProducts);

        return view('shop.cart', compact('products', 'subTotal', 'shipping', 'total'));
    }


    function decreaseProductQuantity(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return abort(404);
        }

        $cartProducts = Session::get('cart', []);
        if (isset($cartProducts[$id]) && $cartProducts[$id] > 1) {
            $cartProducts[$id] -= 1;
        } else {
            return abort(404);
        }

        Session::put('cart', $cartProducts);
        return response()->json(["url" => url("/cart")]);
    }

    function increaseProductQuantity(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return abort(404);
        }

        $cartProducts = Session::get('cart', []);
        if (isset($cartProducts[$id])) {
            $cartProducts[$id] += 1;
        } else {
            return abort(404);
        }

        Session::put('cart', $cartProducts);
        return response()->json(["url" => url("/cart")]);
    }

    function removeProductFromCart(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return abort(404);
        }

        $cartProducts = Session::get('cart', []);
        unset($cartProducts[$id]);
        Session::put('cart', $cartProducts);
        return response()->json(["url" => url("/cart")]);
    }

    function addProductToCart(Request $request)
    {
        if ($request->has('id')) {
            $cartProducts = Session::get('cart', []);
            $currentProductId = (int) $request->get('id');
            if (!array_key_exists($currentProductId, $cartProducts)) {
                $cartProducts[$currentProductId] =  $request->get('quantity', 1);
            } else {
                $cartProducts[$currentProductId] += $request->get('quantity', 1);
            }

            $cartProductsCount = array_reduce($cartProducts, fn ($count, $value) => $count += $value, 0);
            Session::put('cart', $cartProducts);
            // $cartProducts ===> [id1 => quantity1, id2 => quantity2, ... ]

            return response()->json(["count" => $cartProductsCount, "cartUrl" => "/cart"]);
        }

        return abort(404);
    }
}