<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('categories')->latest()->get();
        return response()->json($articles);
    }

    public function categories()
    {
        return response()->json(ArticleCategory::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'categories' => 'required|array|max:2',
            'categories.*' => 'exists:article_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Upload gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/articles', 'public');
        }

        // Simpan artikel
        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'image' => $imagePath,
        ]);

        // Simpan relasi kategori (max 2)
        $article->categories()->attach($request->categories);

        return response()->json([
            'message' => 'Artikel berhasil dibuat',
            'article' => $article->load('categories'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'categories' => 'required|array|max:2',
            'categories.*' => 'exists:article_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update gambar kalau ada
        if ($request->hasFile('image')) {
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
            $article->image = $request->file('image')->store('uploads/articles', 'public');
        }

        // Update data artikel
        $article->update($request->only('title', 'content', 'author'));

        // Update kategori
        $article->categories()->sync($request->categories);

        return response()->json([
            'message' => 'Artikel berhasil diperbarui',
            'article' => $article->load('categories'),
        ]);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json(['message' => 'Artikel berhasil dihapus']);
    }
}
