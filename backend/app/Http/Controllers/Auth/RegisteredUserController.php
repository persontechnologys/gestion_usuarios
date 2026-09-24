<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Exige una cédula ecuatoriana de diez dígitos que no esté registrada.
            'cedula' => ['required', 'digits:10', 'unique:'.User::class.',cedula'],
        ], [
            // Mensaje en español cuando no se envía la cédula.
            'cedula.required' => 'La cédula es obligatoria.',
            // Mensaje en español cuando la cédula no tiene diez dígitos.
            'cedula.digits' => 'La cédula debe tener 10 dígitos.',
            // Mensaje en español cuando la cédula ya pertenece a otro usuario.
            'cedula.unique' => 'La cédula ya se encuentra registrada.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Guarda la cédula validada del nuevo usuario.
            'cedula' => $request->cedula,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
