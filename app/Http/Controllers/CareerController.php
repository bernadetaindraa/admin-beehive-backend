<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    // 🔹 Get all careers
    public function index()
    {
        return response()->json(Career::latest()->get());
    }

    // 🔹 Create new career
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'qualifications' => 'required|string',
            'benefits' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'location' => 'required|string|max:255',
            'work_type' => 'required|in:WFO,WFH,Hybrid',
            'deadline' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $career = Career::create($validator->validated());

        return response()->json([
            'message' => 'Career successfully created',
            'career' => $career
        ], 201);
    }

    // 🔹 Update existing career
    public function update(Request $request, $id)
    {
        $career = Career::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'qualifications' => 'required|string',
            'benefits' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'location' => 'required|string|max:255',
            'work_type' => 'required|in:WFO,WFH,Hybrid',
            'deadline' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $career->update($validator->validated());

        return response()->json([
            'message' => 'Career updated successfully',
            'career' => $career
        ]);
    }

    // 🔹 Delete career
    public function destroy($id)
    {
        $career = Career::findOrFail($id);
        $career->delete();

        return response()->json(['message' => 'Career deleted successfully']);
    }
}
