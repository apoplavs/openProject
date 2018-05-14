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
	public static function getAllJudges($sorting) {
		$sort = $sorting ? 'DESC' : 'ASC';
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status', DB::raw('DATE_FORMAT(judges.updated_status, "%d/%c/%Y") AS updated_status'),	'judges.rating',
			DB::raw('(CASE WHEN user_bookmark_judges.id = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			->orderBy('judges.surname', $sort)
			->paginate(15));
	}
}
