<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Administrator accounts (there is no public registration).
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('create', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);

        User::create($data + ['is_admin' => true]);

        return back()->with('success', "Administrator {$data['name']} added. Share the password with them securely.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot remove your own account.');
        }
        if (User::query()->where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'The website needs at least one administrator.');
        }

        // Keep the account (edits stay attributed) but remove dashboard access.
        $user->forceFill(['is_admin' => false])->save();

        return back()->with('success', "{$user->name} can no longer access the dashboard.");
    }
}
