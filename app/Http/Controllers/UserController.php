<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('isActive', $request->status);
        }

        $users = $query->get();
        $roles = [
            'admin' => 'Admin',
            'kasir' => 'Kasir',
        ];
        $totalUser = $users->count();

        return view('admin.user.index', compact('users', 'roles', 'totalUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'role' => 'required',
            'nama' => 'required',
            'isActive' => 'required'
        ], [
            'password.min' => 'Password harus lebih dari 8 karakter',
            'password.required' => 'Password harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'role.required' => 'Role harus dipilih',
            'nama.required' => 'Nama harus diisi',
            'isActive.required' => 'Status harus dipilih'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return redirect()->route('user.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required',
            'nama' => 'required',
            'isActive' => 'required'
        ], [
            'password.min' => 'Password harus lebih dari 8 karakter',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'role.required' => 'Role harus dipilih',
            'nama.required' => 'Nama harus diisi',
            'isActive.required' => 'Status harus dipilih'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('user.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Data berhasil dihapus');
    }

    public function getRole()
    {
        //
    }

    public function changePassword(Request $request)
    {
        //
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $users = User::where(function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%");
        })->get();

        $roles = [
            'admin' => 'Admin',
            'kasir' => 'Kasir',
        ];
        $totalUser = $users->count();

        return view('admin.user.index', compact('users', 'roles', 'totalUser'));
    }
}
