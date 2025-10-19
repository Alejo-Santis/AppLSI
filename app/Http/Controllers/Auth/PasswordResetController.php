<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use SweetAlert2\Laravel\Swal;

class PasswordResetController extends Controller
{
    /**
     * Mostrar el formulario de solicitud de recuperación
     */
    public function showForgotForm()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Enviar el enlace de recuperación por email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            Swal::success([
                'title' => 'Enlace enviado',
                'text' => 'Te hemos enviado un enlace de recuperación a tu correo.',
                'icon' => 'success'
            ]);
            return back()->with('status', __($status));
        }

        Swal::error([
            'title' => 'Error',
            'text' => 'No encontramos un usuario con ese correo electrónico.',
            'icon' => 'error'
        ]);

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }


    /**
     * Mostrar el formulario de restablecimiento de contraseña
     */
    public function showResetForm(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Restablecer la contraseña
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Intentar restablecer la contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            Swal::success([
                'title' => 'Reset',
                'text' => 'Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión.',
                'icon' => 'success'
            ]);
            return redirect()->route('login');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
