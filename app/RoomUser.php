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
        $this->is_ready = false;
        $this->score = 0;
        $this->save();
    }

    //Взять пользователей по id игры(всех в комнате)
    public static function getUsersByGameId($game_id)
    {
        // Найти всех пользователей по id игры
        // (и полную информацию по ним)
        // Select * from users join room_user on users.id = room_user.user_id where room_user.game_id = $game_id
        // id|name|email      |email_verified_at      |password                                                    |remember_token|created_at             |updated_at             |id|user_id|game_id|is_ready|score|
        // --+----+-----------+-----------------------+------------------------------------------------------------+--------------+-----------------------+-----------------------+--+-------+-------+--------+-----+
        // 1|Ваня|1@gmail.com|2023-07-08 20:44:00.000|$2y$10$xb.VIlR1kUpBXQMFnA1JZO7fTEkAhtn75P8QO6NDpokTMrCi.l6F.|              |2023-07-08 20:44:01.000|2023-07-08 20:44:01.000| 2|      1|      4|true    |     |
        // 2|Даня|2@gmail.com|2023-07-08 20:44:01.000|$2y$10$5jK3OL.6PTuj3w64UtMAa.HzDKEwU7bduMKYVjRVpQ.A5/AYlax1G|              |2023-07-08 20:44:01.000|2023-07-08 20:44:01.000| 3|      2|      4|true    |     |
                
        $usersInRoom = RoomUser::join('users', 'users.id', '=', 'room_user.user_id')
            ->where('room_user.game_id', $game_id)
            ->get();
        return $usersInRoom;
    }
}