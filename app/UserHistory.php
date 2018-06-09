<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * зберігає і відображає історію
 * переглядів користувача
 * Class UserHistory
 * @package Toecyd
 */
class UserHistory extends Model
{
	public $timestamps = false;
	protected $fillable = [
		'user', 'judge'
	];
	
	
	/**
	 * якщо судді немає в історії переглядів і закладках користувача
	 * додати нового суддю
	 * в історію переглядів користувача
	 * @param $judge
	 */
	public static function addToHistory($judge) {
		// перевірка чи перебуває суддя в закладках
		$bookmark_judge = DB::table('user_bookmark_judges')
			->select('judge')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		
		// перевірка чи перебуває суддя в історії переглядів користувача
		$history_judge = static::select('judge')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		if ($bookmark_judge || $history_judge) {
			return ;
		}
		static::insert(
			['user' => Auth::user()->id,
			'judge' => $judge]);
	}
	
	
	/**
	 * Отримати останніх 5 суддів, що перебувають
	 * в історії переглядів користувача
	 */
	public static function getHistoryJudges() {
		$overflow_history = static::select('id')
			->where('user', '=', Auth::user()->id)
			->orderBy('user_histories.created_at', 'DESC')
			->offset(5)
			->limit(20)
			->get();
		
		// якщо є застаріла історія - видаляємо
		if ($overflow_history->isNotEmpty()) {
			// формуємо масив з id суддів, які в застарілій історії
			$judges_id = [];
			foreach($overflow_history as $judge_id) {
				$judges_id[] = $judge_id->id;
			}
			static::whereIn('id', $judges_id)
				->delete();
		}
		
		// отримуємо і повертаємо 5 останніх позицій з історії переглядів користувача
		return (DB::table('judges')->select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%c.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%c.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.Auth::user()->id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->join('user_histories', 'user_histories.judge', '=', 'judges.id')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			->where('user_histories.user', '=', Auth::user()->id)
			->orderBy('user_histories.created_at', 'DESC')
			->limit(5)
			->get());
	}
}
