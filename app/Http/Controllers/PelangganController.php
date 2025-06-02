<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::all();
        $totalPelanggan = Pelanggan::count();
        return view('admin.pelanggan.index', compact(['pelanggans', 'totalPelanggan']));
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
    public function store(StorePelangganRequest $request)
    {
        try {
            Pelanggan::create($request->validated());
            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan pelanggan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan)
    {
        try {
            $pelanggan->update($request->validated());
            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui pelanggan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        try {
            $pelanggan->delete();
            return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus pelanggan');
        }
    }

    public function tambahPoint()
    {
        //
    }

    public function gunakanPoint()
    {
        //
    }

    public function updateLevel()
    {
        //
    }

    public function getByKode(Request $request)
    {
        //
    }

    public function searchByName(Request $request)
    {
        //
    }

    public function getRiwayatTransaksi()
    {
        //
    }
}
