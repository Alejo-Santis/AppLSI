<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SweetAlert2\Laravel\Swal;

class UserController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('employee');

        return Inertia::render('App/User/Profile', [
            'user' => $user,
        ]);
    }

    /**
     * Mostrar la configuración de cuenta
     */
    public function account(Request $request)
    {
        $user = $request->user();

        return Inertia::render('App/User/Account', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Manejo del avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back();
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            Swal::success([
                'title' => 'Foto eliminada',
                'text' => 'Tu foto de perfil ha sido eliminada correctamente.',
                'icon' => 'success'
            ]);
        } else {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se encontró ninguna foto para eliminar.',
                'icon' => 'error'
            ]);
        }

        return back();
    }

    /**
     * Actualizar la información básica del usuario (nombre, email)
     */
    public function updateAccountInfo(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        Swal::success([
            'title' => 'Información actualizada',
            'text' => 'Tu información de cuenta ha sido actualizada correctamente.',
            'icon' => 'success',
        ]);

        return back();
    }

    /**
     * Actualizar la contraseña del usuario
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Swal::success([
            'title' => 'Contraseña actualizada',
            'text' => 'Tu contraseña se ha cambiado correctamente.',
            'icon' => 'success',
        ]);

        return back();
    }
}
