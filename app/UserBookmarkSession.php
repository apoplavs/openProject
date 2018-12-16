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
}
