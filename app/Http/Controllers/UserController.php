<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Untuk registrasi pengguna baru
    public function register(Request $request)
    {
        // Validasi inputan
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // password_confirmation field harus ada di request
        ]);

        // Membuat user baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Membuat token untuk autentikasi (untuk API)
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Untuk login pengguna
    public function login(Request $request)
    {
        // Validasi inputan
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Mencoba autentikasi pengguna
        if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = Auth::user();
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Jika autentikasi gagal
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Untuk logout pengguna
    public function logout()
    {
        Auth::logout(); // Menghapus token
        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    // Untuk mendapatkan data profil pengguna yang sedang login
    public function profile()
    {
        // Mendapatkan data user yang sedang login
        return response()->json(Auth::user());
    }

    // Untuk memperbarui profil pengguna
    public function updateProfile(Request $request)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed', // password_confirmation field harus ada di request
        ]);

        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Memperbarui data pengguna
        if ($validatedData['name'] ?? null) {
            $user->name = $validatedData['name'];
        }
        if ($validatedData['email'] ?? null) {
            $user->email = $validatedData['email'];
        }
        if ($validatedData['password'] ?? null) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
