<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserBookmarkCourt
 * @package Toecyd
 */
class UserBookmarkCourt extends Model
{
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'court'
	];
	
	
	/**
	 * створює нову закладку на суд
	 * @param $user_id
	 * @param $court_id
	 */
	public static function createBookmark($user_id, $court_id) {
		static::insert([
			'user'=>$user_id,
			'court'=>$court_id
		]);
	}
	
	
	/**
	 * видаляє закладку поточного користувача на суд
	 * @param $user_id
	 * @param $court_id
	 */
	public static function deleteBookmark($user_id, $court_id) {
		static::where('user', '=', $user_id)
			->where('court', '=', $court_id)
			->delete();
	}
	
	
	/**
	 * перевірити чи є суд в закладках користувача
	 * @param $user_id
	 * @param $court_id
	 * @return boolean
	 */
	public static function checkBookmark($user_id, $court_id) {
		$bookmark = static::select('id')
			->where('court', '=', $court_id)
			->where('user', '=', $user_id)
			->first();
		if (empty($bookmark)) {
			return (false);
		}
		return (true);
	}
	
	
	/**
	 * Отримати всі суди, що перебувають
	 * в закладках користувача
	 * @return mixed
	 */
	public static function getBookmarkCourts() {
        return (
            call_user_func_array(['Toecyd\Court', 'select'], self::getBookmarkFields())
                ->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
                ->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
                ->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
                ->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
                ->join('user_bookmark_courts', 'user_bookmark_courts.court', '=', 'courts.court_code')
                ->where('user_bookmark_courts.user', '=', Auth::user()->id)
                ->get()
                ->unique(function ($item){return $item->toArray();}) // залишаємо в колекції лише унікальні елементи
                ->values() // перенумеровуємо елементи колекції (після unique вони занумеровані не підряд)
        );
	}

    /**
     * Отримати всі поля, які треба вибрати
     * в закладках користувача
     * @return array
     */
    public static function getBookmarkFields() {
        return [
            'courts.court_code',
            'courts.name AS court_name',
            DB::raw('instances.name AS instance'),
            DB::raw('regions.name AS region'),
            DB::raw('jurisdictions.title AS jurisdiction'),
            'courts.address',
            DB::raw('CONCAT(judges.surname, " ", judges.name, " ", judges.patronymic) AS head_judge'),
            'courts.rating',
        ];
    }
}
