<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{

    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        return $this->cartService->addToCart($request->all()); 
    }

    /**
     * Remove a product from the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart(Cart $cart)
    {
        try {
            $cart->delete();
            return response()->json(['message' => 'Product removed from cart.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Product not found in cart.'], 404);
        } 
    }

    /**
     * Display all items in the cart for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewCart()
    {
        $userId = auth()->id();

        $cartItems = Cart::where('user_id', $userId)->with('itemable')->get();

        return Inertia::render('Cart', [
            'cartItems' => $cartItems
        ]);
    }
}
