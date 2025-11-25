<?php

namespace App\Http\Controllers;

use App\Models\Career;

class PublicCareerController extends Controller
{
    public function index()
    {
        $careers = Career::where('deadline', '>=', now()->toDateString())
                         ->orWhereNull('deadline')
                         ->latest()
                         ->get();

        return response()->json($careers);
    }

    public function show($id)
    {
        $career = Career::findOrFail($id);
        
        if ($career->deadline && $career->deadline < now()->toDateString()) {
            abort(404);
        }

        return response()->json($career);
    }
}