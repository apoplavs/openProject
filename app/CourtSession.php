<?php

namespace Toecyd;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * модель для роботи з таблицею court_sessions
 * Class CourtSession
 * @package Toecyd
 */
class CourtSession extends Model
{
	
	/**
	 * @param $courtCode
	 * @param $date
	 * @param $number
	 * @return mixed
	 */
	public static function getCourtSessionId($courtCode, $date, $number) {
        return DB::table('court_sessions')
            ->select('id')
            ->where('court', '=', $courtCode)
            ->where('date', '=', $date)
            ->where('number', '=', $number)
            ->value('id');
    }
	
	
	/**
	 * Отримує судові засідання для сторінки профайлу судді
	 * логіка роботи функції в даному запиті:
	 * 1. якщо суддя один тобто judge2 IS NULL AND judge3 IS NULL
	 * 		то отримати ПІБ цього судді
	 * 2. якщо суддів 2 тобто judge2 IS NOT NULL AND judge3 IS NULL
	 * 		то "головуючий суддя: " + ПІБ судді1 + ПІБ судді2
	 * 3. якщо суддів 3 тобто ELSE
	 *        то отримати ПІБ всіх суддів і скласти рядки з відповідними вставками
	 * 			"головуючий суддя: " + ПІБ судді1 +
	 * 			"; учасник колегії: " + ПІБ судді2 +
	 * 			"; учасник колегії: " + ПІБ судді3
	 *
	 * @param $judge
	 */
	public static function getSessionByJudge($judge) {
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return(static::select('court_sessions.id', 'court_sessions.date', 'court_sessions.number',
			 DB::raw(' get_judges_by_id(judge1, judge2, judge3) AS judges'),
			DB::raw('justice_kinds.name AS forma'),
			 'court_sessions.involved', 'court_sessions.description',
			DB::raw("(CASE WHEN user_bookmark_sessions.user = {$user_id} THEN 1 ELSE 0 END) AS is_bookmark"))
			->leftJoin('user_bookmark_sessions', 'user_bookmark_sessions.user', '=', 'court_sessions.judge1')
			->join('justice_kinds', 'justice_kinds.justice_kind', '=', 'court_sessions.forma')
			->whereRaw("DATE(court_sessions.date) >= CURDATE() AND ".
				"(court_sessions.judge1={$judge} OR court_sessions.judge2={$judge} OR court_sessions.judge3={$judge})")
			->orderBy('court_sessions.date', 'ASC')
			->get());
    }
	
	/**
	 * Отримує судові засідання для сторінки профайлу судді
	 *
	 * @param $judge
	 */
	public static function getSessionByJudgeGuest($judge) {
		return(static::select('court_sessions.date', 'court_sessions.number',
			DB::raw(' get_judges_by_id(judge1, judge2, judge3) AS judges'),
			DB::raw('justice_kinds.name AS forma'),
			'court_sessions.involved', 'court_sessions.description')
			->join('justice_kinds', 'justice_kinds.justice_kind', '=', 'court_sessions.forma')
			->whereRaw("DATE(court_sessions.date) >= CURDATE() AND ".
				"(court_sessions.judge1={$judge} OR court_sessions.judge2={$judge} OR court_sessions.judge3={$judge})")
			->orderBy('court_sessions.date', 'ASC')
			->get());
	}
	
	
	/**
	 * перевірити чи існує засідання з даним id
	 *
	 * @param $id
	 * @return boolean
	 */
	public static function checkSessionById($id) {
		$judge = static::select('court_sessions.id')
			->where('court_sessions.id', '=', $id)
			->first();
		
		return !empty($judge);
	}
	
	


    /**
	 * Отримує судові засідання для сторінки суду
	 * @param $judge
	 */
	public static function getSessionByCourt($court_code) {
		
		// 
		
    }
	
	
	/**
	 * отримати найближче майбутнє судове засідання за номером справи
	 * якщо воно існує
	 */
	public static function getFutureSession($number) {
		$future_session = static::select('court_sessions.id',
			DB::raw('DATE_FORMAT(`court_sessions`.`date`, "%d.%m.%Y") AS date_session'),
			DB::raw('DATE_FORMAT(`court_sessions`.`date`, "%H:%i") AS time_session'))
			->where('court_sessions.date', '>', Carbon::now('Europe/Kiev'))
			->where('court_sessions.number', '=', $number)
			->first();
		return ($future_session);
	}
}
