<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AdminService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Create a new user (teacher or student).
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createUser(array $data)
    {
        return $this->userRepo->create($data);
    }

    /**
     * Get all users (teachers and students).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllUsers()
    {
        return $this->userRepo->all();
    }

    /**
     * Get a user by ID.
     *
     * @param int $id
     * @return \App\Models\User
     */
    public function getUserById(int $id)
    {
        return $this->userRepo->find($id);
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id): void
    {
        $user = $this->userRepo->find($id);
        $this->userRepo->delete($user);
    }
}
