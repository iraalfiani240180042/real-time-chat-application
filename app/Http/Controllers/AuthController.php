<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // LOGIN PAGE
    public function login()
    {
        return view('auth.login');
    }

    // REGISTER PAGE
    public function register()
    {
        return view('auth.register');
    }

    // REGISTER USER
    public function registerPost(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login');
    }

    // LOGIN USER
    public function loginPost(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

    $request->session()->regenerate();

    Auth::user()->update([
        'is_online' => true
    ]);

    return redirect('/chat');
}

        return back()->with('error', 'Email atau password salah');
    }

    // LOGOUT
   public function logout()
{
    if(auth()->check())
    {
        $user = auth()->user();

        $user->is_online = false;

        $user->save();
    }

    Auth::logout();

    return redirect('/login');
}
}