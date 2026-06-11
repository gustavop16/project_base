<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Services\UploadService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private User $user_model,
        private UserService $user_service,
        private UploadService $upload_service
    ) {}

    public function index()
    {
        $users = $this->user_model->with('vessels:id,name')->get();
        return UserResource::collection($users);
    }

    public function store(UserRequest $request)
    {
        $plain = $this->user_service->generatePassword();
        $input = $this->user_service->prepareData(
            $request->except('vessel_ids'), $plain
        );

        if ($request->hasFile('photo')) {
            $file           = $this->upload_service->upload('users/', $request->file('photo'));
            $input['photo'] = $file['file'];
        }

        $user = $this->user_model->create($input);
        $user->syncRoles([strtolower($input['type'])]);
        $user->vessels()->sync($request->vessel_ids ?? []);
        $user->notify(new UserCreatedNotification($plain));

        return new UserResource($user->load('vessels'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $input = $request->except('password');

        if ($request->hasFile('photo')) {
            $file           = $this->upload_service->upload('users/', $request->file('photo'));
            $input['photo'] = $file['file'];
        }

        $user->update($input);

        if (isset($input['type'])) {
            $user->syncRoles([strtolower($input['type'])]);
        }

        if ($request->has('vessel_ids')) {
            $user->vessels()->sync($request->vessel_ids ?? []);
        }

        return new UserResource($user->load('vessels'));
    }

    public function showProfile(Request $request)
    {
        return new UserResource($request->user()->load('vessels'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'observations' => 'nullable|string',
        ]);

        $user->update($request->only('name', 'email', 'phone', 'observations'));

        return new UserResource($user->load('vessels'));
    }

    public function updatePassword(NewPasswordRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $input = $this->user_service->prepareDataPassword($request->all());
        $user->update($input);

        return new UserResource($user);
    }

    public function updatePhoto(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($request->hasFile('files')) {
            $photo          = $this->upload_service->upload('profile/' . $user->id, $request->file('files'));
            $user->update(['photo' => $photo['file']]);
        }

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'ok'], 200);
    }
}
