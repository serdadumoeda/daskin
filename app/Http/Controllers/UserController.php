<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        // Logika Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        // Logika Filter Peran
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->input('role')));
        }

        $users = $query->paginate(10);
        $roles = Role::pluck('name', 'name');

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        // Ambil semua peran KECUALI superadmin untuk ditampilkan di form
        $roles = Role::where('name', '!=', 'superadmin')->pluck('name');
        return view('users.create', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array', 'min:1'], // Pastikan minimal satu peran dipilih
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Gunakan syncRoles untuk menetapkan peran yang dipilih dari form
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(User $user)
    {
        $roles = Role::where('name', '!=', 'superadmin')->pluck('name');
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update($request->only('name', 'email'));
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
        
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        if ($user->hasRole('superadmin')) {
             return back()->with('error', 'Super Admin tidak dapat dihapus.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}