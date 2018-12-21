<?php

namespace Toecyd;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserBookmarkSession
 * @package Toecyd
 */
class UserBookmarkSession extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'court_session', 'note'
    ];


    /**
     * Отримати всі поля, які треба вибрати
     * в закладках користувача
     * @return array
     */
    public static function getBookmarkFields()
    {
        return [
            'court_sessions.id',
            'court_sessions.date',
            DB::raw('get_judges_by_id(court_sessions.judge1, court_sessions.judge2, court_sessions.judge3) AS judges'),
            'courts.court_code',
            'courts.name',
            'court_sessions.number',
            'court_sessions.involved',
            'court_sessions.description',
        ];
    }


    /**
     * створює закладку
     * @param $user_id
     * @param $court_session_id
     */
    public static function createBookmark($user_id, $court_session_id)
    {
        static::insert([
                           'user'          => $user_id,
                           'court_session' => $court_session_id,
                       ]);
    }


    /**
     * видаляє закладку
     * @param $user_id
     * @param $court_session_id
     */
    public static function deleteBookmark($user_id, $court_session_id)
    {
        static::where('user', '=', $user_id)
              ->where('court_session', '=', $court_session_id)
              ->delete();
    }

    /**
     * перевірити, чи є судове засідання в закладках користувача
     * @param $user_id
     * @param $court_session_id
     * @return boolean
     */
    public static function checkBookmark($user_id, $court_session_id) {
        return !empty(static::select('id')
                            ->where('court_session', '=', $court_session_id)
                            ->where('user', '=', $user_id)
                            ->first()
                     );
    }


    /**
     * Отримати всі засідання, що перебувають
     * в закладках користувача
     * @return mixed
     */
    public static function getBookmarks()
    {
        return (
        call_user_func_array(['Toecyd\CourtSession', 'select'], self::getBookmarkFields())
            ->join('user_bookmark_sessions', 'court_sessions.id', '=', 'user_bookmark_sessions.court_session')
            ->join('courts', 'court_sessions.court', '=', 'courts.court_code')
            ->where('user_bookmark_sessions.user', '=', Auth::user()->id)
            ->get()
            ->unique(function ($item) {
                return $item->toArray();
            })// залишаємо в колекції лише унікальні елементи
            ->values() // перенумеровуємо елементи колекції (після unique вони занумеровані не підряд)
        );
    }
	
	
	/**
	 * отримати всі закладки для всіх користувачів
	 * на судові засідання які вже пройшли тобто <= current_day
	 *
	 */
	public static function getPastSession() {
    	return (static::select('user_bookmark_sessions.id', 'court_sessions.number')
			->join('court_sessions', 'court_sessions.id', '=', 'user_bookmark_sessions.court_session')
			->where('court_sessions.date', '<', Carbon::now('Europe/Kiev'))
			->get()
		);
	}
	
	
	/**
	 * Оновити закладку користувача
	 * встановити id майбутнього судового засідання
	 * @param $new_session
	 */
	public static function updateUserBookmark(int $bookmark_id, int $new_session) {
		static::where('user_bookmark_sessions.id', '=', $bookmark_id)
			->update(['user_bookmark_sessions.court_session' => $new_session]);
		
	}
	
	/**
	 * записує або оновлює примітку до закладки
	 * @param $bookmark_id
	 * @param $note
	 */
	public static function writeNoteForBookmark(int $bookmark_id, string $note)
	{
		static::where('user_bookmark_sessions.id', '=', $bookmark_id)
			->update(['user_bookmark_sessions.note' => $note]);
	}
	
	
	/**
	 * перевірити чи існує закладка на судове засідання з даним id
	 *
	 * @param $id
	 * @return boolean
	 */
	public static function checkSessionBookmarkById($id) {
		$bookmark = static::select('user_bookmark_sessions.id')
			->where('user_bookmark_sessions.id', '=', $id)
			->first();
		
		return !empty($bookmark);
	}
	
	
	/**
	 * перевірити чи існує закладка на судове засідання з даним id
	 *
	 * @param $id
	 * @param $user_id
	 * @return boolean
	 */
	public static function checkAccessToBookmark($id, $user_id) {
		$bookmark = static::select('user_bookmark_sessions.id')
			->where('user_bookmark_sessions.id', '=', $id)
			->where('user_bookmark_sessions.user', '=', $user_id)
			->first();
		
		return !empty($bookmark);
	}
	
	/**
	 * отримати всіх отримувачів email для Notification3
	 * про зміну статусу судді всім користувачам, які відстежують
	 * судові засідання з участю даного судді
	 * @param $judge_id
	 */
	public static function getRecipientsN3($judge_id) {
		return (static::select(
			DB::raw(' DISTINCT `users`.`email`'),
			'users.name', 'court_sessions.number',
			DB::raw('DATE_FORMAT(`court_sessions`.`date`, "%d.%m.%Y") AS date_session'),
			DB::raw('DATE_FORMAT(`court_sessions`.`date`, "%H:%i") AS time_session'),
			DB::raw('`courts`.`name` AS court_name'))
			->join('user_settings', 'user_settings.user', '=', 'user_bookmark_sessions.user')
			->join('users', 'users.id', '=', 'user_bookmark_sessions.user')
			->join('court_sessions', 'court_sessions.id', '=', 'user_bookmark_sessions.court_session')
			->join('courts', 'courts.court_code', '=', 'court_sessions.court')
			->where('user_settings.email_notification_3', '=', 1)
			->whereRaw(" DATE(`court_sessions`.`date`) >= CURDATE() ")
			->where(function ($query) use($judge_id) {
				$query->where('court_sessions.judge1', '=', $judge_id)
					->orWhere('court_sessions.judge2', '=', $judge_id)
					->orWhere('court_sessions.judge3', '=', $judge_id);
			})
			->get());
	}
}
