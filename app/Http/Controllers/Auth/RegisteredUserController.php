<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected LogService $logService
    ) {}

    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->logService->request($request)->task('register_new_user')->start();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // default roles
        $defaultRole = Role::firstOrCreate(['name' => Role::ROLE_USER]);

        $user->roles()->attach($defaultRole->id);

        event(new Registered($user));

        Auth::login($user);

        $this->logService->status('success')
            ->code(201)
            ->level('info')
            ->response(['user' => $user])
            ->save();

        return to_route('dashboard');
    }

    public function updateUserRoles(Request $request)
    {
        $this->logService->request($request)->task('update_user_roles')->start();

        $rules = [
            'user_id' => ['required', 'exists:users,id'],
            'add_role_name' => ['sometimes', 'nullable', 'array'],
            'add_role_name.*' => ['required', 'string', 'exists:roles,name'],
            'remove_role_name' => ['sometimes', 'nullable', 'array'],
            'remove_role_name.*' => ['required', 'string', 'exists:roles,name'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
            // throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $user = User::findOrFail($validated['user_id']);

        // Handle adding roles
        if ($request->has('add_role_name')) {
            $rolesToAdd = Role::whereIn('name', $validated['add_role_name'])->pluck('roles.id')->toArray();
            $user->roles()->attach(Arr::except($rolesToAdd, $user->roles()->pluck('roles.id')->toArray()));
        }

        // Handle removing roles
        if ($request->has('remove_role_name')) {
            $rolesToRemove = Role::whereIn('name', $validated['remove_role_name'])->pluck('roles.id')->toArray();
            $user->roles()->detach($rolesToRemove);
        }

        $this->logService->status('success')
            ->code(200)
            ->level('info')
            ->response(['user' => $user, 'roles' => $user->roles ?? null])
            ->save();

        return to_route('dashboard')->with('success', 'User roles updated successfully.');
    }
}
