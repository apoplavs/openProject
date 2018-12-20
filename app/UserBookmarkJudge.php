<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserBookmarkJudge
 * @package Toecyd
 */
class UserBookmarkJudge extends Model
{
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'judge'
	];
	
	
	/**
	 * створює нову закладку на суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function createBookmark($user_id, $judge_id) {
		static::insert([
			'user'=>$user_id,
			'judge'=>$judge_id
		]);
	}
	
	
	/**
	 * видаляє закладку поточного користувача на суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function deleteBookmark($user_id, $judge_id) {
		static::where('user', '=', $user_id)
			->where('judge', '=', $judge_id)
			->delete();
	}
	
	
	/**
	 * Отримати всіх суддів, що перебувають
	 * в закладках користувача
	 * @return mixed
	 */
	public static function getBookmarkJudges() {
		return (
		    call_user_func_array(['Toecyd\Judge', 'select'], self::getBookmarkFields())
                ->join('courts', 'judges.court', '=', 'courts.court_code')
                ->join('user_bookmark_judges', 'user_bookmark_judges.judge', '=', 'judges.id')
                ->where('user_bookmark_judges.user', '=', Auth::user()->id)
                ->get()
                ->unique(function ($item){return $item->toArray();}) // залишаємо в колекції лише унікальні елементи
                ->values() // перенумеровуємо елементи колекції (після unique вони занумеровані не підряд)
        );
	}
	
	
	/**
	 * перевірити чи є суддя в закладках користувача
	 * @param $user_id
	 * @param $judge_id
	 * @return boolean
	 */
	public static function checkBookmark($user_id, $judge_id) {
		$bookmark = static::select('id')
			->where('judge', '=', $judge_id)
			->where('user', '=', $user_id)
			->first();
		if (empty($bookmark)) {
			return (false);
		}
		return (true);
	}

    /**
     * Отримати всі поля, які треба вибрати
     * в закладках користувача
     * @return array
     */
    public static function getBookmarkFields() {
        return [
            'judges.id',
            'courts.name AS court_name',
            'judges.surname',
            'judges.name',
            'judges.patronymic',
            'judges.photo',
            'judges.status',
            DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
            DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
            'judges.rating'
        ];
    }
	
	/**
	 * отримати всіх отримувачів email для Notification1
	 * про зміну статусу судді користувачам, які його відстежують
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function getRecipientsN1($judge_id) {
		return (static::select('users.name', 'users.email')
			->join('user_settings', 'user_settings.user', '=', 'user_bookmark_judges.user')
			->join('users', 'users.id', '=', 'user_bookmark_judges.user')
			->where('user_settings.email_notification_1', '=', 1)
			->where('user_bookmark_judges.judge', '=', $judge_id)
			->get());
	}
}
