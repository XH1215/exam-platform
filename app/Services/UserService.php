<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return $this->users->create($data);
    }
    public function updateProfile(int $userId, array $data): User
    {
        if (isset($data['role'])) {
            unset($data['role']);
        }
        return $this->users->update($userId, $data);
    }
    public function changePassword(int $userId, string $currentPassword, string $newPassword): User
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
    public function deleteUser(int $id): void
    {
        $this->users->delete($id);
    }
}
