<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProductService;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['productService', 'industry'])->latest()->get();
        return response()->json($projects);
    }

    public function dropdowns()
    {
        return response()->json([
            'product_services' => ProductService::all(),
            'industries' => Industry::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'goal' => 'required|string',
            'product_service_id' => 'required|exists:product_services,id',
            'industry_id' => 'required|exists:industries,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/projects', 'public');
        }

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'goal' => $request->goal,
            'product_service_id' => $request->product_service_id,
            'industry_id' => $request->industry_id,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project->load(['productService', 'industry']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'goal' => 'required|string',
            'product_service_id' => 'required|exists:product_services,id',
            'industry_id' => 'required|exists:industries,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            $project->image = $request->file('image')->store('uploads/projects', 'public');
        }

        $project->update($request->only([
            'title', 'description', 'location', 'goal',
            'product_service_id', 'industry_id'
        ]));

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project->load(['productService', 'industry']),
        ]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        if ($project->image && Storage::disk('public')->exists($project->image)) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
