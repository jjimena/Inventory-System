<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    public function index(): Response
    {
        return response()
            ->view('auth.login');
    }

    public function store(): RedirectResponse
    {
        request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!auth()->attempt(request()->only(['email', 'password']))) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.'
            ])->onlyInput('email');
        }

        request()->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::DASHBOARD);
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
