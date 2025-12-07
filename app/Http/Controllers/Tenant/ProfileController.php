<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdatePasswordRequest;
use App\Http\Requests\Tenant\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): Response
    {
        return Inertia::render('Tenant/Profile/Edit', [
            'user' => auth()->user()->only(['name', 'email']),
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('tenant.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()
            ->route('tenant.profile.edit')
            ->with('success', 'Password updated successfully.');
    }
}
