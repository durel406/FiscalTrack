<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    
    public function page()
{
    return view('comptes.index', [
        'initialUsers' => \App\User::latest()->get(), // adaptez au modèle/champs réels
    ]);
}
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->ensureAdmin();

        return response()->json([
            'users' => User::orderBy('name')->get()->map(function (User $user) {
                return $this->toArray($user);
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $this->validated($request, true);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
            'identifiant' => $data['role'] === 'admin' ? ($data['identifiant'] ?? null) : null,
        ]);

        return response()->json([
            'message' => 'Compte créé.',
            'user' => $this->toArray($user),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();

        $user = User::findOrFail($id);
        $data = $this->validated($request, false, $user->id);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->status = $data['status'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Compte mis à jour.',
            'user' => $this->toArray($user->fresh()),
        ]);
    }

    public function toggleStatus($id)
    {
        $this->ensureAdmin();

        $user = User::findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez pas suspendre votre propre compte.'], 422);
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'message' => $user->status === 'active' ? 'Compte réactivé.' : 'Compte suspendu.',
            'user' => $this->toArray($user),
        ]);
    }

    public function destroy($id)
    {
        $this->ensureAdmin();

        $user = User::findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Compte supprimé.']);
    }

    private function validated(Request $request, bool $creating, $ignoreId = null): array
    {
        $rules = [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreId),
            ],
            'role' => ['required', 'string', 'max:100'],
            'statut' => ['required', 'in:active,inactive'],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:6'],
        ];

        $validated = $request->validate($rules);

        return [
            'name' => trim($validated['prenom'].' '.$validated['nom']),
            'email' => $validated['email'],
            'password' => $validated['password'] ?? null,
            'role' => $this->normalizeRole($validated['role']),
            'status' => $validated['statut'],
            'identifiant' => $request->input('identifiant'),
        ];
    }

    private function normalizeRole(string $role): string
    {
        $map = [
            'Administrateur' => 'admin',
            'Comptable' => 'comptable',
            'Responsable fiscal' => 'responsable_fiscal',
            'admin' => 'admin',
            'comptable' => 'comptable',
            'responsable_fiscal' => 'responsable_fiscal',
            'fiscal' => 'responsable_fiscal',
        ];

        return $map[$role] ?? $role;
    }

    private function roleLabel(string $role): string
    {
        $map = [
            'admin' => 'Administrateur',
            'comptable' => 'Comptable',
            'responsable_fiscal' => 'Responsable fiscal',
            'fiscal' => 'Responsable fiscal',
        ];

        return $map[$role] ?? $role;
    }

    private function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'nom' => $user->name,
            'email' => $user->email,
            'role' => $this->roleLabel($user->role),
            'role_key' => $user->role === 'responsable_fiscal' ? 'fiscal' : $user->role,
            'statut' => $user->status ?: 'active',
        ];
    }

    private function ensureAdmin(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
    }
}
