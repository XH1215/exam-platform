<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function register(array $data, $role = 'student')
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $role;
        return $this->users->create($data);
    }

    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Generate API token (using Laravel Sanctum)
            $token = $user->createToken('api-token')->plainTextToken;
            return ['user' => $user, 'token' => $token];
        }
        throw new \Exception('Invalid credentials');
    }

    public function updateProfile($userId, array $data)
    {
        $user = $this->users->find($userId);
        // Prevent role change
        unset($data['role']);
        $this->users->update($user, $data);
        return $user;
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $user = $this->users->find($userId);
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }
        $user->password = Hash::make($newPassword);
        $user->save();
        return $user;
    }

    public function allUsers()
    {
        return $this->users->all();
    }

    public function deleteUser(int $id)
    {
        $user = $this->users->find($id);
        $this->users->delete($user);
    }
}
