<?php

namespace App\Http\Controllers;

use App\Models\Article;

class PublicArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('categories')
            ->latest()
            ->get()
            ->map(function ($article) {
                if ($article->image) {
                    $article->image = asset('storage/' . ltrim($article->image, '/'));
                }
                $article->date_upload = $article->created_at?->format('d M Y');
                $article->categories_list = $article->categories->pluck('name')->toArray();
                return $article;
            });

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::with('categories')->findOrFail($id);
        
        if ($article->image) {
            $article->image = asset('storage/' . ltrim($article->image, '/'));
        }
        $article->date_upload = $article->created_at?->format('d M Y');
        $article->categories_list = $article->categories->pluck('name')->toArray();

        return response()->json($article);
    }
}