<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class CartService
{
    public function addToCart(array $data)
    {
        try {
            DB::beginTransaction();
            $productId = $data['product_id'];
            $userId = auth()->id();
            $cartItem = Cart::where('user_id', $userId)
                ->where('itemable_id', $productId)
                ->where('itemable_type', Product::class)
                ->first();
    
            if ($cartItem) {
                return response()->json(['type' => 'error', 'message' => 'Product already exists in cart.']);
            }
            Cart::create([
                'user_id' => $userId,
                'itemable_id' => $productId,
                'itemable_type' => Product::class,
                'quantity' => 1,
            ]);
            DB::commit();
    
            return response()->json(['type' => 'success', 'message' => 'Product added to cart.']);

        }
        catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }  

    }
}
