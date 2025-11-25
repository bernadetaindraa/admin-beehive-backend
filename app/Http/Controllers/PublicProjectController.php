<?php

namespace App\Http\Controllers;

use App\Models\Project;

class PublicProjectController extends Controller
{
    public function index()
    {
        return Project::with(['productService', 'industry'])
            ->latest()
            ->get()
            ->map(function ($project) {
                if ($project->image) {
                    $clean = ltrim($project->image, '/');
                    $project->image = url('storage/' . $clean);
                }

                return $project;
            });
    }
}
