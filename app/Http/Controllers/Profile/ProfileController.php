<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::with(['role', 'pejabat'])->findOrFail(auth()->id());
        return view('profile.show', compact('user'));
    }

    public function showChangePassword()
    {
        $user = User::with(['role'])->findOrFail(auth()->id());
        return view('profile.change-password', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'nama' => ['required', 'min:3', 'max:255', 'regex:/^[A-Za-z\s\.,]+$/'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/', 'unique:users,username,' . $user->id],
            // 'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'nama.regex' => 'Nama hanya boleh mengandung huruf, spasi, titik, dan koma.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.regex' => 'Username harus diawali huruf, hanya boleh berisi huruf/angka, serta titik (.), strip (-), atau underscore (_) di tengah (tidak boleh berurutan atau di akhir).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            // 'email.required' => 'Email wajib diisi.',
            // 'email.email' => 'Format email tidak valid.',
            // 'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        try {
            $user->nama = $request->nama;
            $user->username = $request->username;
            // $user->email = $request->email;
            $user->save();

            ActivityLogger::log('User', 'ubah', 'Memperbarui data profil sendiri', $user->id, $user->nama);

            return redirect()->route('profile')->with('success', 'Data profil berhasil diperbarui!');
        } catch (\Throwable $e) {
            Log::error('ProfileController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            ActivityLogger::log('User', 'ubah', 'Mengubah password sendiri', $user->id, $user->nama);

            return redirect()->route('profile.password')->with('success', 'Password berhasil diubah!');
        } catch (\Throwable $e) {
            Log::error('ProfileController::changePassword error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
}
