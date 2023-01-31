<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{

    use \App\Traits\CartSummaryTrait;

    function index()
    {
        $cartProducts = Session::get('cart', []);
        if (count($cartProducts) === 0) {
            return redirect("/cart")->with("error", "Nothing in cart to submit as an order");
        }
        $products = $this->mapSessionProducts($cartProducts);
        $subTotal = $this->calculateSubTotal($cartProducts);
        $shipping = $this->calculateShipping($cartProducts);
        $total = $this->calculateTotal($cartProducts);

        return view('shop.checkout', compact('products', 'subTotal', 'shipping', 'total'));
    }


    function createOrder(Request $request) {
        $request->validate(Order::$rules);

        // get cart items from session
        $cartProducts = Session::get('cart', []);
        $products = $this->mapSessionProducts($cartProducts);


        $subTotal = $this->calculateSubTotal($cartProducts);
        $shipping = $this->calculateShipping($cartProducts);
        $total = $this->calculateTotal($cartProducts);


        $currentAuthUserId = Auth::user()->id; 

        // add order
        $order = new Order();
        $order->fill($request->post());
        $order['user_id'] = $currentAuthUserId;
        $order['subtotal'] = $subTotal;
        $order['shipping'] = $shipping;
        $order['total_price'] = $total;
        $order->save();

        // add order details
        foreach($products as $product) {
            $orderDetail = new OrderDetail();
            $orderDetail['order_id'] = $order->id;
            $orderDetail['product_id'] = $product['product']['id'];
            $orderDetail['quantity'] = $product['quantity'];
            $orderDetail['price'] = ($product['product'])->getPriceAfterDiscount();
            $orderDetail->save();
        }

        // clear cart from products using
        Session::forget('cart');
        return redirect("/")->with("success", "Your order is submitted successfully");
    }
}