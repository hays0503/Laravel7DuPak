<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['name'];

    public function createGame($name)
    {
        $game = $this->create(['name' => $name]);

        // Дополнительная логика создания игры

        return $game;
    }

    public function deleteGame($id)
    {
        $game = $this->findOrFail($id);

        // Дополнительная логика перед удалением игры

        $game->delete();

        // Дополнительная логика после удаления игры
    }
}