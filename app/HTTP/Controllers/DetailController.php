<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\Review;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    function index(Request $request) {
        $id = $request->get('id');
        $product = Product::findOrFail($id);
        $products = Product::where('category_id', $product->category_id)->get();
        $colors = Color::all();
        $sizes = Size::all();
        $reviews = Review::where('product_id', $id)->with('user')->get();
        return view('shop.detail', compact('product', 'sizes', 'colors', 'products', 'reviews'));
    }

    function postReview(Request $request) {
        $request->validate(Review::$rules);
        if (!$request->has('id')) {
            return abort(404);
        }

        if (!Auth::user()) {
            return abort(401);
        }

        $productId = (int) $request->get('id');
        $userId = Auth::user()->id;

        $review = new Review();
        $review['reveiw'] = $request->get('review');
        $review['product_id'] = $productId;
        $review['user_id'] = $userId;
        $review['rating'] = (float) $request->get('rating');
        $review['name'] = $request->get('name');
        $review['email'] = $request->get('email');
        $review->save();

        // get rating_count and sum of rating from reviews table to that specific product
        $productReviews = Review::where('product_id', '=', $productId)->get();
        $productTotalRating = $productReviews->sum('rating');
        $productReviewsCount = $productReviews->count();

        // get the specific product and and the values of rating_count and rating to reduce the
        // calculation process of the rating and rating count for specific product.
        $product = Product::findOrFail($productId);
        $product['rating_count'] = $productReviewsCount;
        $product['rating'] = floor(($productTotalRating / $productReviewsCount) * 2) / 2; // get avergae rating and round it down to nearest half integer
        $product->save();

        return redirect("/detail?id=" . $productId)->with('success', 'your review is added successfully');
    }
}