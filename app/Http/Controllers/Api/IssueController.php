<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index()
    {
        return response()->json(
            Issue::with('user:id,name')->latest()->paginate(20)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_block' => 'nullable|string',
            'location_room' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['user_id'] = $request->user()->id;

        $issue = Issue::create($validated);

        return response()->json($issue, 201);
    }

    public function show(Issue $issue)
    {
        return response()->json($issue->load('user:id,name'));
    }

    public function update(Request $request, Issue $issue)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        if ($issue->user_id !== $user->id && !$isAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'category' => 'sometimes|string',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location_block' => 'nullable|string',
            'location_room' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'sometimes|in:pending,in_progress,resolved',
        ]);

        if (isset($validated['status']) && !$isAdmin) {
            return response()->json(['message' => 'Only admins can change status'], 403);
        }

        $issue->update($validated);

        return response()->json($issue);
    }

    public function destroy(Request $request, Issue $issue)
    {
        $user = $request->user();

        if ($issue->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $issue->delete();

        return response()->json(['message' => 'Issue deleted']);
    }
}