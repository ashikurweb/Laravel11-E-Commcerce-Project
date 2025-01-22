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

    public function ajaxIncreaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $newQty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $newQty);

        return response()->json([
            'success' => true,
            'newQty' => $newQty,
            'newSubtotal' => Cart::instance('cart')->get($rowId)->subtotal,
        ]);
    }

    public function ajaxDecreaseCartQuantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $newQty = max(1, $product->qty - 1); // Qty cannot be less than 1
        Cart::instance('cart')->update($rowId, $newQty);

        return response()->json([
            'success' => true,
            'newQty' => $newQty,
            'newSubtotal' => Cart::instance('cart')->get($rowId)->subtotal,
        ]);
    }

    public function removeItem(Request $request, $rowId)
    {
        try {
            // Remove the item from the cart
            Cart::instance('cart')->remove($rowId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove the item',
            ]);
        }
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }
}
