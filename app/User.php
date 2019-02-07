<?php

namespace Toecyd;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

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
        'name', 'surname', 'phone', 'facebook_id', 'google_id', 'email', 'password', 'photo', 'usertype', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password', 'remember_token'
    ];
	
	
	/**
	 * перевірка користувача
	 * повернення повідомлення про помилку || true
	 * @param array $credentials
	 * @return bool
	 */
	public static function checkUser(array $credentials) {

		$user = static::where('email', '=', $credentials['email'])
			->first();
		
		if (empty($user) || $user->usertype == 4) {
			return Lang::get('passwords.user'); // користувача не існує
		} else if ($user->usertype == 7) { //todo пізніше виправити на 1 // не підтверджений email
			return Lang::get('auth.unconfirmed');
		} else if (!is_null($user->google_id) && !Auth::validate($credentials)) {
			return Lang::get('auth.via_google');
		} else if (!is_null($user->facebook_id) && !Auth::validate($credentials)) {
			return Lang::get('auth.via_facebook');
		}
		return true;
	}

	public static function getPhotoStorage() {
        return Storage::disk('public');
    }
	
	
	/**
	 * отримує всі налаштування поточного користувача
	 * реалізовано в 2 запити оскільки так зручніше на фронті
	 * @return array
	 */
	public static function getSettings() : array {
		$profile = static::select('users.name', 'users.surname', 'users.phone', 'users.phone', 'users.email', 'users.photo')
			->where('users.id', '=', Auth::user()->id)
			->first();

		$notifications = DB::table('user_settings')
			->select('user_settings.email_notification_1', 'user_settings.email_notification_2',
			'user_settings.email_notification_3', 'user_settings.email_notification_4',
			'user_settings.email_notification_5', 'user_settings.email_notification_6')
			->where('user_settings.user', '=', Auth::user()->id)
			->first();


		return (['profile' => $profile, 'notifications' => $notifications]);
	}
}
