<?php

namespace Toecyd;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Модель для зв`язку з таблицею courts
 * Class Court
 * @package Toecyd
 */
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
        return (static::select('court_code')
            ->whereNotIn('region_code', [1, 5, 12, 27]) //відкидаємо АР Крим, Донецьку, Луганську області
            ->where('court_code', '<', 2800) // відкидаємо спеціалізовані суди
            ->pluck('court_code'));
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
		return (static::select('courts.court_code', 'courts.name AS court_name',
			DB::raw('instances.name AS instance'), DB::raw('regions.name AS region'),
			DB::raw('jurisdictions.title AS jurisdiction'), 'courts.address',
			DB::raw('CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) AS head_judge'), 'courts.rating',
			DB::raw('(CASE WHEN user_bookmark_courts.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
			->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
			->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
			->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
			->leftJoin('user_bookmark_courts', function ($join)use($user_id) {
				$join->on('courts.court_code', '=', 'user_bookmark_courts.court');
				$join->on('user_bookmark_courts.user', '=',  DB::raw($user_id));
			})
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
				return $query->where('courts.name', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('courts.name', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('courts.name', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('courts.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('courts.rating', 'DESC');
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
		return (static::select('courts.court_code', 'courts.name AS court_name',
			DB::raw('instances.name AS instance'), DB::raw('regions.name AS region'),
			DB::raw('jurisdictions.title AS jurisdiction'), 'courts.address',
			DB::raw('CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) AS head_judge'), 'courts.rating')
			->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
			->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
			->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
			->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
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
				return $query->where('courts.name', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('courts.name', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('courts.name', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('courts.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('courts.rating', 'DESC');
			})
			->paginate(10));
	}
	
	
	
	/**
	 * отримує результати автодоповнення
	 * для поля пошуку суду
	 * @param string $search
	 * @return mixed
	 */
	public static function getAutocomplete(string $search) {
		
		$results = static::select('courts.court_code', 'courts.name')
			->where('courts.name', 'LIKE', $search.'%')
			->limit(5)
			->get();
		return ($results);
		
	}
	
	
	/**
	 * перевірити чи існує суд з даним id
	 * @param $id
	 * @return boolean
	 */
	public static function checkCourtById($id) {
		$court = static::select('courts.court_code')
			->where('courts.court_code', '=', $id)
			->first();
		
		return !empty($court);
	}
	
}
