<?php

namespace Toecyd;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
	public $timestamps = false;
	// The attributes that are mass assignable.
	protected $fillable = [
		'address', 'phone', 'email', 'site'
	];
	
	
	/**
	 * Отримання кодів судів для консольних команд
	 * @return mixed
	 */
	public static function getCourtCodes()
    {
        return DB::table('courts')
            ->select('court_code')
            ->whereNotIn('region_code', [1, 5, 12]) //відкидаємо АР Крим, Донецьку, Луганську області
            ->where('court_code', '<', 2800) // відкидаємо спеціалізовані суди
            ->pluck('court_code');
    }
	
	
	
	/**
	 * отримати список суддів, враховуючи фільтри, які були задані
	 * @param $regions  array
	 * @param $instances  array
	 * @param $jurisdictions  array
	 * @param $sort_order  integer
	 * @param $search  string
	 * @param $powers_expired  boolean
	 * @return mixed
	 */
	public static function getCourtsList($regions, $instances, $jurisdictions, $sort_order, $search) {
		
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('courts.court_code', 'courts.name AS court_name', 'instances.name', 'regions.name',
			'jurisdictions.title', 'courts.address',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			// фільтрція за регіоном
			->when(!empty($regions), function ($query) use ($regions) {
				return $query->whereIn('courts.region_code', $regions);
			})
			// фільтрція за інстанцією
			->when(!empty($instances), function ($query) use ($instances) {
				return $query->whereIn('courts.instance_code', $instances);
			})
			// фільтрція за юрисдикцією
			->when(!empty($jurisdictions), function ($query) use ($jurisdictions) {
				return $query->whereIn('courts.jurisdiction', $jurisdictions);
			})
			// якщо застосовано пошук
			->when(!empty($search), function ($query) use ($search) {
				return $query->where('judges.surname', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('judges.surname', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('judges.surname', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('judges.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('judges.rating', 'DESC');
			})
			->paginate(10));
	}
	
	/**
	 * Для не зареєстрованого користувача
	 * отримати список суддів, враховуючи фільтри, які були задані
	 * @param $regions  array
	 * @param $instances  array
	 * @param $jurisdictions  array
	 * @param $sort_order  integer
	 * @param $search  string
	 * @param $powers_expired  boolean
	 * @return mixed
	 */
	public static function getCourtsListGuest($regions, $instances, $jurisdictions, $sort_order, $search) {
		
		// отримання id користувача
		return (static::select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating')
			->join('courts', 'judges.court', '=', 'courts.court_code')
			// фільтрція за регіоном
			->when(!empty($regions), function ($query) use ($regions) {
				return $query->whereIn('courts.region_code', $regions);
			})
			// фільтрція за інстанцією
			->when(!empty($instances), function ($query) use ($instances) {
				return $query->whereIn('courts.instance_code', $instances);
			})
			// фільтрція за юрисдикцією
			->when(!empty($jurisdictions), function ($query) use ($jurisdictions) {
				return $query->whereIn('courts.jurisdiction', $jurisdictions);
			})
			// якщо застосовано пошук
			->when(!empty($search), function ($query) use ($search) {
				return $query->where('judges.surname', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('judges.surname', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('judges.surname', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('judges.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('judges.rating', 'DESC');
			})
			->paginate(10));
	}
	
}
