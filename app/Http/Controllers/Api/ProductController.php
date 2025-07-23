<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //all products
        $products = \App\Models\Product::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Product',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category_id' => 'required',
            'is_best_seller' => 'nullable|integer',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'description' => 'nullable|string|max:1000'
        ]);

        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('products', $filename, 'public');
        $category = \App\Models\Category::where('id', $request->category_id)->first();
        
        $product = \App\Models\Product::create([
            'name' => $request->name,
            'price' => (int) $request->price,
            'stock' => (int) $request->stock,
            'category_id' => $request->category_id,
            'category' => $category->name,
            'image' => $filename,
            'is_best_seller' => $request->is_best_seller ?? 0,
            'description' => $request->description
        ]);

        if ($product) {
            return response()->json([
                'success' => true,
                'message' => 'Product Created',
                'data' => $product
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product Failed to Save',
            ], 409);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Product Details',
            'data' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $request->validate([
            'name' => 'required|min:3',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category_id' => 'required',
            'is_best_seller' => 'nullable|integer',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $filename, 'public');
            $product->image = $filename;
        }

        $category = \App\Models\Category::where('id', $request->category_id)->first();
        
        $product->name = $request->name;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category_id = $request->category_id;
        $product->category = $category->name;
        $product->is_best_seller = $request->is_best_seller ?? 0;
        $product->description = $request->description;
        $product->save();
        return response()->json([
            'success' => true,
            'message' => 'Product Updated',
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        if ($product->image) {
            \Storage::delete('public/products/' . $product->image);
        }
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product Deleted',
        ], 200);
    }
}
