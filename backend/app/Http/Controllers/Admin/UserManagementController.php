<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    use EnsuresAdminAccess;

    public function index(): View
    {
        $this->ensureOwnerAdmin();

        return view('admin.users.index', [
            'users' => User::query()
                ->latest()
                ->paginate(20),
            'roleLabels' => $this->roleLabels(),
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function create(): View
    {
        $this->ensureOwnerAdmin();

        return view('admin.users.form', [
            'user' => new User([
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
            ]),
            'roleLabels' => $this->roleLabels(),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'title' => 'Add member',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureOwnerAdmin();

        $data = $this->validatedUserData($request);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Member created successfully.');
    }

    public function edit(User $user): View
    {
        $this->ensureOwnerAdmin();

        return view('admin.users.form', [
            'user' => $user,
            'roleLabels' => $this->roleLabels(),
            'statusLabels' => $this->statusLabels(),
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
            'title' => 'Edit member',
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureOwnerAdmin();

        $data = $this->validatedUserData($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Member updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureOwnerAdmin();

        abort_if($user->is(auth()->user()), 422, 'You cannot delete the account currently in use.');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Member deleted successfully.');
    }

    private function validatedUserData(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'confirmed', Password::min(8)]
            : ['required', 'confirmed', Password::min(8)];

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'line_id' => ['nullable', 'string', 'max:80'],
            'role' => ['required', Rule::in(array_keys($this->roleLabels()))],
            'status' => ['required', Rule::in(array_keys($this->statusLabels()))],
            'password' => $passwordRules,
        ]);
    }

    private function roleLabels(): array
    {
        return [
            User::ROLE_CUSTOMER => 'Customer',
            User::ROLE_STAFF => 'Staff',
            User::ROLE_ADMIN => 'Admin',
        ];
    }

    private function statusLabels(): array
    {
        return [
            User::STATUS_ACTIVE => 'Active',
            User::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    private function ensureOwnerAdmin(): void
    {
        abort_unless(auth()->user()?->isOwnerAdmin(), 403);
    }
}
