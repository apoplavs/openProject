<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserLikedJudge
 * @package Toecyd
 */
class UserLikedJudge extends Model
{
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'judge'
	];
	
	
	
	/**
	 * повертає TRUE якщо користувач поставив лайк/дизлайк судді
	 * або FALSE якщо ні
	 * @param $judge
	 * @return bool
	 */
	public static function isLikedJudge($judge) {
		$data_status = static::select('like', 'unlike')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && ($data_status->like || $data_status->unlike))  ? (true) : (false));
	}
}
