<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelangganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:255|unique:pelanggans,kode,' . $this->pelanggan->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pelanggans,email,' . $this->pelanggan->id,
            'point' => 'required|integer|min:0',
            'level' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode pelanggan harus diisi',
            'kode.unique' => 'Kode pelanggan sudah ada',
            'nama.required' => 'Nama pelanggan harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'point.required' => 'Point harus diisi',
            'point.integer' => 'Point harus berupa angka',
            'point.min' => 'Point minimal 0',
            'level.required' => 'Level harus diisi',
        ];
    }
}
