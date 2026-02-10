<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('addresses');

        // NAME filter
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.trim($request->name).'%');
        }

        // EMAIL filter
        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.trim($request->email).'%');
        }

        // PHONE filter
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%'.trim($request->phone).'%');
        }

        // IS_ACTIVE filter
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('addresses.country', 'roles');

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name_pt')->get();

        return view('admin.users.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Address fields
            'title' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'max:50'],
            'address_phone' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'country_id' => ['required', 'exists:countries,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Create address for the user
        $user->addresses()->create([
            'title' => $request->title,
            'nif' => $request->nif,
            'phone' => $request->address_phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'is_default' => true,
        ]);

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        $user->load('addresses.country');
        $countries = Country::where('is_active', true)->orderBy('name_pt')->get();

        return view('admin.users.edit', compact('user', 'countries'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users.show', $user);
    }

    public function updateAddress(Request $request, User $user, Address $address)
    {
        // Ensure address belongs to user
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'country_id' => ['required', 'exists:countries,id'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $address->update($request->only([
            'title', 'nif', 'phone', 'address_line_1', 'address_line_2',
            'postal_code', 'city', 'country_id', 'is_default',
        ]));

        return redirect()->route('admin.users.edit', $user);
    }

    public function createAddress(Request $request, User $user)
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'country_id' => ['required', 'exists:countries,id'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $user->addresses()->create([
            'title' => $request->title,
            'nif' => $request->nif,
            'phone' => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('admin.users.edit', $user);
    }
}
