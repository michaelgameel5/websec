<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProductsController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

    public function list(Request $request)
    {
        $query = Product::query();

        // Your excellent filtering system
        $query->when($request->keywords, 
            fn($q) => $q->where("name", "like", "%$request->keywords%"));
        
        $query->when($request->min_price, 
            fn($q) => $q->where("price", ">=", $request->min_price));
        
        $query->when($request->max_price, 
            fn($q) => $q->where("price", "<=", $request->max_price));
        
        $query->when($request->order_by, 
            fn($q) => $q->orderBy($request->order_by, $request->order_direction ?? "ASC"));

        $products = $query->get();

        return view('products.list', compact('products'));
    }

    public function edit(Request $request, Product $product = null)
    {
        // Only allow employees and admins to access
        if (!auth()->user()->isEmployee() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $product = $product ?? new Product();
        return view('products.edit', compact('product'));
    }

    public function save(Request $request, Product $product = null)
    {
        // Only allow employees and admins to access
        if (!auth()->user()->isEmployee() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $this->validate($request, [
            'code' => ['required', 'string', 'max:32', 
                      Rule::unique('products')->ignore($product->id ?? null)],
            'name' => ['required', 'string', 'max:128'],
            'model' => ['required', 'string', 'max:256'],
            'description' => ['required', 'string', 'max:1024'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048']
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            if ($product->exists && $product->photo) {
                Storage::delete($product->photo);
            }
            $validated['photo'] = $request->file('photo')->store('product-images');
        }

        DB::transaction(function() use ($validated, $product) {
            $product = $product ?? new Product();
            $product->fill($validated);
            $product->save();
        });

        return redirect()->route('products_list')
               ->with('success', 'Product saved successfully');
    }

    public function delete(Request $request, Product $product)
    {
        // Only allow employees and admins to access
        if (!auth()->user()->isEmployee() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        DB::transaction(function() use ($product) {
            if ($product->photo) {
                Storage::delete($product->photo);
            }
            $product->delete();
        });

        return redirect()->route('products_list')
               ->with('success', 'Product deleted successfully');
    }
	
	public function updateStock(Request $request, Product $product)
	{
		$request->validate([
			'stock' => 'required|integer|min:0',
		]);

		$product->stock = $request->stock;
		$product->save();

		return redirect()->route('products_list')->with('success', 'Stock updated successfully.');
	}
}