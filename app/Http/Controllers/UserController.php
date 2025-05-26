<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $roles = [
            'admin' => 'Admin',
            'kasir' => 'Kasir',
        ];
        return view('admin.user.index', compact('users', 'roles'));
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
            'username' => 'required|unique:users',
            'password' => 'min:8',
            'role' => 'required',
            'nama' => 'required',
            'isActive' => 'required'
        ]);
        if ($validated['password'] == null) {
            unset($validated['password']);
        }
        $validated['password'] = bcrypt($validated['password']);
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
}
