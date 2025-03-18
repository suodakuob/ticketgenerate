<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $query = User::query();
        
        // If super admin, show all users except themselves
        if (Auth::user()->isSuperAdmin()) {
            $query->where('id', '!=', Auth::id());
        } 
        // If admin, show all users except themselves
        else {
            $query->where('id', '!=', Auth::id());
        }
        
        $users = $query->latest()->paginate(10);
        $roles = [
            'ROLE_USER' => User::ROLE_USER,
            'ROLE_ADMIN' => User::ROLE_ADMIN,
            'ROLE_SUPER_ADMIN' => User::ROLE_SUPER_ADMIN,
        ];
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Auth::user()->isSuperAdmin() 
            ? [User::ROLE_USER => 'User', User::ROLE_ADMIN => 'Admin'] 
            : [User::ROLE_USER => 'User'];
            
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . User::ROLE_USER . ',' . User::ROLE_ADMIN,
        ]);

        // Only super admin can create admin users
        if ($validated['role'] === User::ROLE_ADMIN && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action. Only super admin can create admin users.');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Everyone can view user details, but only super admin can view their own details
        if ($user->id === Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'You cannot view your own profile here. Please use the profile page.');
        }

        return view('admin.users.show', [
            'user' => $user,
            'roles' => [
                'ROLE_USER' => User::ROLE_USER,
                'ROLE_ADMIN' => User::ROLE_ADMIN,
                'ROLE_SUPER_ADMIN' => User::ROLE_SUPER_ADMIN,
            ]
        ]);
    }

    public function edit(User $user)
    {
        // Prevent editing super admin (id=1)
        if ($user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Admin can only edit regular users
        if (!Auth::user()->isSuperAdmin() && $user->role === User::ROLE_ADMIN) {
            abort(403, 'Unauthorized action. Admins can only edit regular users.');
        }

        // Get available roles based on current user's permissions
        $roles = Auth::user()->isSuperAdmin()
            ? [
                User::ROLE_USER => 'User',
                User::ROLE_ADMIN => 'Admin',
                User::ROLE_SUPER_ADMIN => 'Super Admin'
            ]
            : [User::ROLE_USER => 'User'];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Prevent updating super admin (id=1)
        if ($user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Admin can only update regular users
        if (!Auth::user()->isSuperAdmin() && $user->role === User::ROLE_ADMIN) {
            abort(403, 'Unauthorized action. Admins can only update regular users.');
        }

        // Validate basic fields
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Add role validation if user is super admin
        if (Auth::user()->isSuperAdmin()) {
            $rules['role'] = 'required|in:' . implode(',', [User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]);
        }

        $validated = $request->validate($rules);

        // Build update data
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Add password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Add role if super admin
        if (Auth::user()->isSuperAdmin() && isset($validated['role'])) {
            $updateData['role'] = $validated['role'];
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting super admin (id=1)
        if ($user->isSuperAdmin()) {
            abort(403, 'Unauthorized action. Super admin cannot be deleted.');
        }

        // Admin can only delete regular users
        if (!Auth::user()->isSuperAdmin() && $user->role === User::ROLE_ADMIN) {
            abort(403, 'Unauthorized action. Admins can only delete regular users.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
