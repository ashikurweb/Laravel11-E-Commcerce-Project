<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function addToCart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back()->with('status', 'Product Added to Cart Successfully.');
    }

    public function increaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);

        return response()->json([
            'success' => true,
            'subTotal' => Cart::instance('cart')->subtotal(),
            'total' => Cart::instance('cart')->total(),
            'tax' => Cart::instance('cart')->tax(),
        ]);
    }

    public function decreaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty > 1 ? $product->qty - 1 : 1; // Minimum 1
        Cart::instance('cart')->update($rowId, $qty);

        return response()->json([
            'success' => true,
            'subTotal' => Cart::instance('cart')->subtotal(),
            'total' => Cart::instance('cart')->total(),
            'tax' => Cart::instance('cart')->tax(),
        ]);
    }
}
