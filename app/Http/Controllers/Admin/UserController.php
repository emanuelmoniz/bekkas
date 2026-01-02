<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // ACTIVE FLAG (explicit handling)
        $user->active = $request->has('active');
        $user->save();

        // ROLE UPDATE
        if ($request->filled('role')) {
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return redirect()->route('admin.users.show', $user);
    }
}
