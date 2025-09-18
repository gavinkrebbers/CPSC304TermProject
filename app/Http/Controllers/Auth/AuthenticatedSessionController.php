<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    // gets the current session
    public function create(Request $request): Response
    {
        $sessionId = $request->cookie(config('session.cookie'));
        $status = null;

        if ($sessionId) {
            $session = DB::selectOne('SELECT * FROM sessions WHERE id = ? LIMIT 1', [$sessionId]);

            if ($session) {
                $data = unserialize(base64_decode($session->payload));
                $status = $data['status'] ?? null;
            }
        }

        return Inertia::render('Auth/Login', [
            'status' => $status,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::selectOne('SELECT * FROM users WHERE email = ? LIMIT 1', [$request->email]);


        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Auth::loginUsingId($user->id);

        $request->session()->regenerate();

        return redirect()->intended(route('welcome'));
    }

    // logout function
    public function destroy(Request $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();

        DB::delete('DELETE FROM sessions WHERE id = ?', [$sessionId]);

        // remake the csrf token
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
