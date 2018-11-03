<?php

namespace Toecyd;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Swagger\Annotations as SWG;

/**
 * Class User
 * Модель для звязку з таблицею users
 * @package Toecyd
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'phone', 'facebook_id', 'google_id', 'email', 'password', 'photo', 'usertype'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
	
	
	/**
	 * перевірка користувача
	 * повернення повідомлення про помилку || true
	 * @param $email
	 * @return bool
	 */
	public static function checkUser($email) {

		$user = static::where('email', '=', $email)
			->first();
		
		if (empty($user) || $user->usertype == 4) {
			return Lang::get('passwords.user'); // користувача не існує
		} else if ($user->usertype == 7) { //todo пізніше виправити на 1 // не підтверджений email
			return Lang::get('auth.unconfirmed');
		} else if (!is_null($user->google_id)) {
			return Lang::get('auth.via_google');
		} else if (!is_null($user->facebook_id)) {
			return Lang::get('auth.via_facebook');
		}
		return true;
	}

	public static function getPhotoStorage()
    {
        return Storage::disk('public');
    }
}
