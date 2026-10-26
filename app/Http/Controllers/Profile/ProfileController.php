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
            'nama' => ['required', 'min:3', 'max:255', "regex:/^[a-zA-Z\s\.,'\-]+$/"],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/', 'unique:users,username,' . $user->id],
            // 'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'nama.regex' => 'Nama hanya boleh berisi huruf, spasi, serta tanda baca nama/gelar (tidak boleh mengandung angka).',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.regex' => 'Username harus diawali huruf, hanya boleh berisi huruf/angka, serta titik (.), strip (-), atau underscore (_) di tengah (tidak boleh berurutan atau di akhir).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            // 'email.required' => 'Email wajib diisi.',
            // 'email.email' => 'Format email tidak valid.',
        ]);

        // Cegah pengguna biasa mengklaim username Super Developer yang dicadangkan sistem
        $superDevUsernames = array_map('strtolower', User::getSuperDeveloperUsernames());
        if (in_array(strtolower($request->username ?? ''), $superDevUsernames, true) && !in_array(strtolower($user->username ?? ''), $superDevUsernames, true)) {
            return redirect()->back()->withInput()->withErrors(['username' => 'Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan.']);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $user->nama = $request->nama;
            $user->username = $request->username;
            // $user->email = $request->email;
            $user->save();

            // Sinkronkan nama pejabat penandatangan jika user terhubung ke record pejabat
            if ($user->pejabat) {
                $user->pejabat->update(['nama' => $user->nama]);
            }

            \Illuminate\Support\Facades\DB::commit();

            ActivityLogger::log('User', 'ubah', 'Memperbarui data profil sendiri', $user->id, $user->nama);

            return redirect()->route('profile')->with('success', 'Data profil berhasil diperbarui!');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('ProfileController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password', Password::defaults()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.different' => 'Password baru tidak boleh sama dengan password saat ini.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        try {
            // Invalidate other sessions in Laravel auth guard
            try {
                \Illuminate\Support\Facades\Auth::logoutOtherDevices($request->current_password);
            } catch (\Throwable $e) {
                Log::warning('Auth::logoutOtherDevices error', ['error' => $e->getMessage()]);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Invalidate other devices in DeviceSessionLimiter and revoke API tokens
            app(\App\Services\Auth\DeviceSessionLimiter::class)->releaseOtherDevices($user, $request);
            $user->tokens()->delete();

            ActivityLogger::log('User', 'ubah', 'Mengubah password sendiri', $user->id, $user->nama);

            return redirect()->route('profile.password')->with('success', 'Password berhasil diubah!');
        } catch (\Throwable $e) {
            Log::error('ProfileController::changePassword error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
}
