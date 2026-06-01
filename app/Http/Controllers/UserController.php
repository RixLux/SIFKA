<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        if ($query = $request->query('q')) {
            $users = User::search($query)->paginate(20);
        } else {
            $users = User::query()->latest()->paginate(20);
        }

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $this->authorize('create', User::class);

        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
        ]);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }

    /**
     * Search for users (Admin only).
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = $request->query('q');
        $users = User::search($query)->get();

        return UserResource::collection($users);
    }
}
