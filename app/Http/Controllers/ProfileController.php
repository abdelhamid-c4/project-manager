<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $stats = [
            'accessible_projects' => $user->accessibleProjects()->count(),
            'owned_projects' => $user->ownedProjects()->count(),
            'assigned_tasks' => $user->tasks()->count(),
            'completed_tasks' => $user->tasks()->where('status', 'done')->count(),
        ];

        return view('profile.show', compact('stats', 'user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'service' => ['required', 'string', Rule::in(['development', 'design', 'marketing', 'support', 'sales', 'other'])],
        ]);

        $user->update($validated);

        ActivityLog::record('profile.updated', $user, 'Updated profile details.');

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
