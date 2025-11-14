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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/articles', 'public');
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'image' => $imagePath,
        ]);

        $article->categories()->attach($request->categories);

        return response()->json([
            'message' => 'Artikel berhasil dibuat',
            'article' => [
                ...$article->toArray(),
                'image_url' => $imagePath ? asset('storage/' . $imagePath) : null
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['_method' => $request->input('_method')]);

        $validator = Validator::make($request->all(), [
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'author'    => 'required|string|max:255',
            'categories' => 'required|array|min:1|max:2',
            'categories.*' => 'exists:article_categories,id',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $article = Article::findOrFail($id);

        // Hapus gambar lama jika upload baru
        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $path = $request->file('image')->store('uploads/articles', 'public');
            $article->image = $path;
        }

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
        ]);

        $article->categories()->sync($request->categories);

        return response()->json([
            'message' => 'Artikel diperbarui',
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'author' => $article->author,
                'image' => $article->image,
                'image_url' => $article->image ? asset('storage/' . $article->image) : null,
                'categories' => $article->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray(),
            ]
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
