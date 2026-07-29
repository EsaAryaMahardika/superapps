<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'custom_username' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_\.]+$/',
                // Unik kecuali milik user sendiri
                'unique:user,custom_username,' . $user->id,
                // Tidak boleh sama dengan NIS user lain
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && User::where('username', $value)->where('id', '!=', $user->id)->exists()) {
                        $fail('Username sudah digunakan oleh akun lain.');
                    }
                },
            ],
            'password'     => 'nullable|string|min:4|confirmed',
        ]);

        $data = [];

        if ($request->filled('custom_username')) {
            $data['custom_username'] = $request->custom_username;
        } elseif ($request->has('clear_custom_username')) {
            $data['custom_username'] = null;
        }

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if (!empty($data)) {
            User::where('id', $user->id)->update($data);
            session()->flash('success', 'Profil berhasil diperbarui.');
        } else {
            session()->flash('info', 'Tidak ada perubahan.');
        }

        return redirect()->back();
    }
}
