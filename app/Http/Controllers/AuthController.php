<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Show Forms ────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:customer,driver'],
        ]);

        // Drivers need admin approval
        $validated['status'] = $validated['role'] === 'driver' ? 'pending' : 'active';
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        Auth::login($user);

        return $this->redirectByRole($user);
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();

        if ($user->status === 'rejected') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda telah ditolak oleh admin.']);
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil keluar.');
    }

    // ── Redirect Helper ───────────────────────────────────────────────────────

    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'driver') {
            return $user->isPending()
                ? redirect()->route('driver.pending')
                : redirect()->route('driver.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }
}
