<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Crypt;


class UserService
{
    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function register(array $data, $role = 'student')
    {
        // Encrypt the plain password
        $data['password'] = Crypt::encryptString($data['password']);
        $data['role'] = $role;

        return $this->users->create($data);
    }

    public function login(array $credentials)
    {
        // 1) Look up user by email
        $user = $this->users->findByEmail($credentials['email']);
        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        // 2) Decrypt the stored password
        try {
            $decrypted = Crypt::decryptString($user->password);
        } catch (\Exception $e) {
            // if decryption fails
            throw new \Exception('Invalid credentials');
        }

        // 3) Compare
        if ($decrypted !== $credentials['password']) {
            throw new \Exception('Invalid credentials');
        }

        // 4) Generate Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
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

        // Decrypt stored
        $decrypted = Crypt::decryptString($user->password);
        if ($decrypted !== $currentPassword) {
            throw new \Exception('Current password is incorrect');
        }

        // Encrypt new one
        $user->password = Crypt::encryptString($newPassword);
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
