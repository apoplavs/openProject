<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Judge extends Model
{
	
	public $timestamps = false;
	 // The attributes that are mass assignable.
	protected $fillable = [
		'surname', 'name', 'patronymic', 'photo', 'facebook', 'chesnosud', 'status', 'phone', 'rating', 'likes', 'unlikes'
	];
	
	
	/**
	 * @return mixed
	 */
	public static function getJudgesList($regions, $instances, $sort_order, $search) {
		
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%c.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%c.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			->when(!empty($regions), function ($query) use ($regions) {
				return $query->whereIn('courts.region_code', $regions);
			})
			->when(!empty($instances), function ($query) use ($instances) {
				return $query->whereIn('courts.instance_code', $instances);
			})
			->when(!empty($search), function ($query) use ($search) {
				return $query->where('judges.surname', 'LIKE', $search.'%');
			})
			->orderBy('judges.surname', $sort_order)
			->paginate(10));
	}
	
	
	/**
	 * отримує результати автодоповнення
	 * для поля пошуку судді
	 * @param string $search
	 * @return mixed
	 */
	public static function getAutocomplete(string $search) {
		
		$results = static::select('judges.surname', 'judges.name', 'judges.patronymic', 'courts.name AS court_name')
		->join('courts', 'judges.court', '=', 'courts.court_code')
		->where('judges.surname', 'LIKE', $search.'%')
		->limit(5)
		->get();
		return ($results);
	
	}
	
	
	/**
	 * встановлює новий статус судді
	 * і повертає його
	 * @param $judge_id
	 * @param $status
	 * @param $due_date
	 */
	public static function setNewStatus($judge_id, $status, $due_date) {
		static::where('judges.id', '=', $judge_id)
			->update(['judges.status' => $status,
				'judges.due_date_status' => $due_date]);
		return (static::select('judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%c.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%c.%Y") AS due_date_status'))
			->where('judges.id', '=', $judge_id)
			->first());
	}
	
	
	
	/**
	 * @param $judge_id
	 */
	public static function getJudgeData($judge_id) {
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('judges.id', 'judges.surname', 'judges.name',
			'courts.name AS court_name', 'courts.address AS court_address',
			'courts.phone AS court_phone', 'courts.email AS court_email', 'courts.site AS court_site',
			'judges.patronymic', 'judges.photo', 'judges.status', 'judges.likes', 'judges.unlikes',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%c.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%c.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			->where('judges.id', '=', $judge_id)
			->first());
	}
}
