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
use Inertia\Inertia;
use Inertia\Response;
use App\Models\EmailTemplate;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response|RedirectResponse
    {
        // Check if registration is enabled (default: enabled)
        $enableRegistration = admin_setting('enableRegistration');

        if ($enableRegistration === 'off') {
            return redirect()->route('login');
        }

        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if registration is enabled (default: enabled)
        $enableRegistration = admin_setting('enableRegistration');

        if ($enableRegistration === 'off') {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $enableEmailVerification = admin_setting('enableEmailVerification');
        $adminUser = User::where('type', 'superadmin')->first();

        // Step 1 : create the user and assign role
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => $enableEmailVerification === 'on' ? null : now(),
                'type' => 'company',
                'lang' => admin_setting('defaultLanguage') ?? 'en',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]);

            User::CompanySetting($user->id);
            User::MakeRole($user->id);
            Role::firstOrCreate(
                ['name' => $user->type, 'guard_name' => 'web'],
                ['label' => ucfirst($user->type), 'editable' => false, 'created_by' => $adminUser?->id]
            );
            $user->assignRole($user->type);

            Auth::login($user);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Registration failed. Please try again.')]);
        }

        // Step 2 : send welcome e-mail (non-blocking)
        if ($adminUser && admin_setting('New User') == 'on') {
            try {
                EmailTemplate::sendEmailTemplate('New User', [$user->email], [
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'password' => $request->password,
                ], $adminUser->id);
            } catch (\Throwable) {}
        }

        // Step 3 : e-mail verification flow
        if ($enableEmailVerification === 'on') {
            try {
                if ($adminUser) {
                    SetConfigEmail($adminUser->id);
                }
                $user->sendEmailVerificationNotification();
                return redirect(route('verification.notice'))
                    ->with('status', 'verification-link-sent');
            } catch (\Throwable) {
                // SMTP not configured — still send to verification page so user can retry
                return redirect(route('verification.notice'))
                    ->withErrors(['email' => __('Failed to send verification email. Please check your email settings or use the resend button.')]);
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
