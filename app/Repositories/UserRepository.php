<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create($data)
    {
        return User::create($data);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function update($id, $data)
    {
        /** @var \App\Models\User $user */
        $user = User::findOrFail($id);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user->update($data);
        return $user;
    }

    public function delete($id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function all()
    {
        return User::all();
    }

    public function find(int $id)
    {
        return User::findOrFail($id);
    }

}
