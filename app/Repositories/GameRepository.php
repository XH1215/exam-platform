<?php

namespace App\Repositories;

use App\Models\Game;

class GameRepository
{
    public function getAll()
    {
        return Game::all();
    }

    public function getById($id)
    {
        return Game::findOrFail($id);
    }

    public function create($data)
    {
        return Game::create($data);
    }

    public function update($id, $data)
    {
        $game = Game::findOrFail($id);
        $game->update($data);
        return $game;
    }

    public function delete($id)
    {
        $game = Game::findOrFail($id);
        return $game->delete();
    }
}
