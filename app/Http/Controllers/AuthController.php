<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role_hint' => ['required'],
        ]);

        $roleMap = [
            'admin' => ['admin'],
            'staff' => ['staff'],
            'barangay' => ['barangay_user'],
            'association' => ['association'],
        ];

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Check if role matches selected Login As
            $allowedRoles = $roleMap[$request->role_hint] ?? [];
            if (!in_array($user->role?->name, $allowedRoles)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account does not match the selected role. Please select the correct "Login As" option.',
                ])->onlyInput('email');
            }

            // Check if account is active
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. Please wait.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $accountType = $request->input('account_type', 'barangay');

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'account_type' => 'required|in:barangay,association',
        ]);

        if ($accountType === 'association') {
            $request->validate([
                'association_name' => 'required|string|max:150',
                'contact_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            $role = \App\Models\Role::where('name', 'association')->first();

            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $role->id,
                'status' => 'inactive',
            ]);

            \App\Models\AssociationAccount::create([
                'user_id' => $user->id,
                'association_name' => $request->association_name,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'approval_status' => 'pending',
            ]);

            return redirect()->route('login')
                ->with('success', 'Registration submitted! Please wait for admin approval before logging in.');
        }

        $request->validate([
            'barangay_id' => 'required|exists:barangays,id',
        ]);

        $role = \App\Models\Role::where('name', 'barangay_user')->first();

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $role->id,
            'status' => 'inactive',
        ]);

        \App\Models\BarangayAccount::create([
            'user_id' => $user->id,
            'barangay_id' => $request->barangay_id,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration submitted! Please wait for admin approval before logging in.');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}