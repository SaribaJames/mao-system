<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Barangay;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['role', 'barangayAccount']);

        // Search by name or email
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by role
        if (request('role')) {
            $query->whereHas('role', function ($q) {
                $q->where('name', request('role'));
            });
        }

        $users = $query->latest()->paginate(15);
        $totalUsers = User::count();
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count();
        $staff = User::whereHas('role', fn($q) => $q->where('name', 'staff'))->count();
        $barangayReps = User::whereHas('role', fn($q) => $q->where('name', 'barangay_user'))->count();

        return view('users.index', compact('users', 'totalUsers', 'admins', 'staff', 'barangayReps'));
    }

    public function create()
    {
        $roles = Role::all();
        $barangays = Barangay::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();
        return view('users.create', compact('roles', 'barangays', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'pin' => 'nullable|digits_between:4,6',
            'module_assignment' => 'nullable|in:programs,stocks',
            'assigned_programs' => 'nullable|array',
            'assigned_programs.*' => 'exists:programs,id',
        ]);

        $role = Role::find($request->role_id);
        $managesStocks = $role->name === 'staff' && $request->module_assignment === 'stocks';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => 'active',
            'pin' => $request->filled('pin') ? Hash::make($request->pin) : null,
            'manages_stocks' => $managesStocks,
        ]);

        if ($role->name === 'barangay_user' && $request->barangay_id) {
            \App\Models\BarangayAccount::create([
                'user_id' => $user->id,
                'barangay_id' => $request->barangay_id,
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }

        if ($role->name === 'staff' && $request->module_assignment === 'programs' && $request->filled('assigned_programs')) {
            Program::whereIn('id', $request->assigned_programs)
                ->update(['assigned_user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', "User {$request->name} created successfully!");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $barangays = Barangay::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();
        $assignedProgramIds = $user->assignedPrograms()->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'barangays', 'programs', 'assignedProgramIds'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'module_assignment' => 'nullable|in:programs,stocks',
            'assigned_programs' => 'nullable|array',
            'assigned_programs.*' => 'exists:programs,id',
        ]);

        $role = Role::find($request->role_id);
        $managesStocks = $role->name === 'staff' && $request->module_assignment === 'stocks';

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'manages_stocks' => $managesStocks,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('pin')) {
            $request->validate(['pin' => 'digits_between:4,6']);
            $data['pin'] = Hash::make($request->pin);
        }

        $user->update($data);

        // Reset: unassign any programs this user currently manages...
        Program::where('assigned_user_id', $user->id)->update(['assigned_user_id' => null]);

        // ...then reassign only if "programs" was chosen (mutually exclusive with stocks)
        if ($role->name === 'staff' && $request->module_assignment === 'programs' && $request->filled('assigned_programs')) {
            Program::whereIn('id', $request->assigned_programs)
                ->update(['assigned_user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} updated successfully!");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('Password@123')]);
        return redirect()->route('users.index')
            ->with('success', "Password for {$user->name} reset to Password@123");
    }

    public function approvePending(User $user)
    {
        $user->update(['status' => 'active']);

        if ($user->barangayAccount) {
            $user->barangayAccount->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} has been approved and can now login!");
    }
}