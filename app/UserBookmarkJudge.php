<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

class UserBookmarkJudge extends Model
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
	 * створює нову закладку за суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function createBookmark($user_id, $judge_id) {
		static::insert([
			'user'=>$user_id,
			'judge'=>$judge_id
		]);
	}
	
	
	/**
	 * видаляє закладку поточного користувача за суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function deleteBookmark($user_id, $judge_id) {
		static::where('user', '=', $user_id)
			->where('judge', '=', $judge_id)
			->delete();
	}
}
