<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function store($productId, Request $request)
    {
        // Ensure user is logged in
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to buy a product.');
        }

        // Find the product
        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Check if the product is in stock
        if ($product->stock < 1) {
            return redirect()->back()->with('error', 'Product is out of stock.');
        }

        $cost = $product->price;

        if ($user->credit < $cost) {
            return redirect()->route('insufficient_credit');
        }
        
        $user->credit -= $cost;
        $user->save();

        // Decrease stock
        $product->stock -= 1;
        $product->save();

        // Save the purchase record
        Purchase::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price_at_purchase' => $product->price,
        ]);

        return redirect()->back()->with('success', 'Product purchased successfully.');
    }
    
}
