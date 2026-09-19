<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();

        return view('profile.index', [
            'profile' => $this->toFront($user),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->name = trim($data['name']);
        $user->email = strtolower(trim($data['email']));
        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour.',
            'profile' => $this->toFront($user->fresh()),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect.',
                'errors'  => ['current_password' => ['Le mot de passe actuel est incorrect.']],
            ], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }

    public function settings()
    {
        return view('settings.index', [
            'profile' => $this->toFront(Auth::user()),
        ]);
    }

    private function toFront($user)
    {
        $roleLabels = [
            'admin' => 'Administrateur',
            'comptable' => 'Comptable',
            'responsable_fiscal' => 'Responsable fiscal',
            'fiscal' => 'Responsable fiscal',
        ];
        $roleKey = $user->role === 'responsable_fiscal' ? 'fiscal' : $user->role;

        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $roleKey,
            'role_label' => $roleLabels[$user->role] ?? $user->role,
            'status'     => $user->status ?: 'active',
            'identifiant'=> $user->identifiant,
            'created_at' => $user->created_at ? $user->created_at->format('d/m/Y') : '—',
        ];
    }
}
