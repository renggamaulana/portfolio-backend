<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::with('category')->paginate(10); // Lazy loading untuk kategori
        return BlogResource::collection($blogs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:blogs',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $blog = Blog::create($validatedData);

        return new BlogResource($blog);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return new BlogResource($blog->load('category')); // Mengambil kategori juga
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'slug' => 'required|string|unique:blogs,slug,' . $blog->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

            // Proses upload gambar baru (jika ada)
        if ($request->hasFile('image')) {
            // Menghapus gambar lama jika ada
            if ($blog->image_url) {
                Storage::delete('public/' . $blog->image_url); // Hapus gambar lama
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('blog_images', 'public');
            $validatedData['image_url'] = $imagePath;
        }

        $blog->update($validatedData);

        return new BlogResource($blog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
