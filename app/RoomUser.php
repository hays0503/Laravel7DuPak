<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'room_user';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'game_id',
    ];

    /**
     * Get the user that belongs to the room.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game that belongs to the user.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function addUserToRoom($user_id, $game_id)
    {
        $this->user_id = $user_id;
        $this->game_id = $game_id;
        $this->save();
    }
}