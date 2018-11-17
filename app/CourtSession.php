<?php

namespace Toecyd;

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
	 * CASE в даному запиті:
	 * 1. якщо суддя один тобто judge2 IS NULL AND judge3 IS NULL
	 * 		то отримати ПІБ цього судді
	 * 2. якщо суддів 2 тобто judge2 IS NOT NULL AND judge3 IS NULL
	 * 		то "головуючий суддя: " + ПІБ судді1 + ПІБ судді2
	 * 3. якщо суддів 3 тобто ELSE
	 *        то отримати ПІБ всіх суддів і скласти рядки з відповідними вставками
	 * 			"головуючий суддя: " + ПІБ судді1 +
	 * 			"; учасник колегії: " + ПІБ судді2 +
	 * 			"; учасник колегії: " + ПІБ судді3
	 * todo в подальшому потрібно буде зробити SQL функцію для даної операції
	 *
	 * @param $judge
	 */
	public static function getSessionByJudge($judge) {
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return(static::select('court_sessions.id', 'court_sessions.date', 'court_sessions.number',
			 DB::raw('(CASE WHEN court_sessions.judge2 IS NULL THEN '.
			'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1) '.
			
			'WHEN court_sessions.judge2 IS NOT NULL AND court_sessions.judge3 IS NULL THEN '.
			'CONCAT("головуючий суддя: ", '.
			'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1), '.
			'(SELECT CONCAT("; ", judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge2)) '.

			'ELSE CONCAT("головуючий суддя: ", '.
			'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1), '.
			'"; учасник колегії: ", '.
			'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge2), '.
			'"; учасник колегії: ", '.
			'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge3)) END) '.
			
			'AS judges'),
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
	 * CASE в даному запиті:
	 * 1. якщо суддя один тобто judge2 IS NULL AND judge3 IS NULL
	 * 		то отримати ПІБ цього судді
	 * 2. якщо суддів 2 тобто judge2 IS NOT NULL AND judge3 IS NULL
	 * 		то "головуючий суддя: " + ПІБ судді1 + ПІБ судді2
	 * 3. якщо суддів 3 тобто ELSE
	 *        то отримати ПІБ всіх суддів і скласти рядки з відповідними вставками
	 * 			"головуючий суддя: " + ПІБ судді1 +
	 * 			"; учасник колегії: " + ПІБ судді2 +
	 * 			"; учасник колегії: " + ПІБ судді3
	 * todo в подальшому потрібно буде зробити SQL функцію для даної операції
	 *
	 * @param $judge
	 */
	public static function getSessionByJudgeGuest($judge) {
		return(static::select('court_sessions.date', 'court_sessions.number',
			DB::raw('(CASE WHEN court_sessions.judge2 IS NULL THEN '.
				'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1) '.
				
				'WHEN court_sessions.judge2 IS NOT NULL AND court_sessions.judge3 IS NULL THEN '.
				'CONCAT("головуючий суддя: ", '.
				'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1), '.
				'(SELECT CONCAT("; ", judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge2)) '.
				
				'ELSE CONCAT("головуючий суддя: ", '.
				'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge1), '.
				'"; учасник колегії: ", '.
				'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge2), '.
				'"; учасник колегії: ", '.
				'(SELECT CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) FROM judges WHERE judges.id=court_sessions.judge3)) END) '.
				
				'AS judges'),
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
}
