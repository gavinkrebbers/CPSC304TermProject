<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->email;
        $name = $request->name;
        $password = Hash::make($request->password);
        $userExists = DB::selectOne('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
        if ($userExists) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered.',
            ]);
        }
        DB::insert('INSERT INTO users (name, email, password) VALUES (?, ?, ?)', [
            $name,
            $email,
            $password,
        ]);

        $userId = DB::getPdo()->lastInsertId();
        $user = DB::selectOne('SELECT * FROM users WHERE id = ? LIMIT 1', [$userId]);
        event(new Registered($user));
        Auth::loginUsingId($userId);

        return redirect(route('welcome', false));
    }
}
