<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::withCount('products')->orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'data' => $categories,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories',
            ],
            [
                'name.required' => 'Nama kategori harus diisi',
                'name.string' => 'Nama kategori harus berupa string',
                'name.max' => 'Nama kategori maksimal 255 karakter',
                'name.unique' => 'Nama kategori sudah ada',
            ]
        );

        $category = Categories::create(
            [
                'name' => $request->name,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Categories::with('products')->find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditemukan',
            'data' => $category,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            ],
            [
                'name.required' => 'Nama kategori harus diisi',
                'name.string' => 'Nama kategori harus berupa string',
                'name.max' => 'Nama kategori maksimal 255 karakter',
                'name.unique' => 'Nama kategori sudah ada',
            ]
        );

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Categories::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori masih memiliki produk',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
            'data' => $category,
        ], 200);
    }
}
