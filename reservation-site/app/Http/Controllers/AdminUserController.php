<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Prevent non-admins from editing other admins
        if ($user->isAdmin() && !auth()->user()->isAdmin()) {
            abort(403, 'You are not authorized to edit an admin user.');
        }

        $request->validate([
            'role' => 'required|in:customer,expert,manager,admin',
        ]);

        // Prevent a user from being promoted to admin by a non-admin
        if ($request->role === 'admin' && !auth()->user()->isAdmin()) {
            abort(403, 'You are not authorized to promote a user to admin.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }
}
