<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                ->orWhere('barcode', 'like', "%$search%");
        }
        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('category_id', $categoryId);
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $query->paginate(20),
            ]
        );
    }
    public function show($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        if (!$product) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Produk Kosong',
                ],
                404
            );
        }
        return response()->json(
            [
                'success' => true,
                'message' => 'Produk ditemukan',
                'data' => $product,
            ]
        );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:products',
            'unit' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',

        ]);

        $product = Product::create($validatedData);

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product,
            ],
            201
        );
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan',
                ],
                404
            );
        }

        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:products,barcode,' . $product->id,
            'unit' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $product->update($validatedData);

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product,
            ]
        );
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan',
                ],
                404
            );
        }
        $product->delete();
        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil dihapuskan',
                'data' => $product,
            ]
        );
    }
}
