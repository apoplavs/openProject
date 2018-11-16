<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserBookmarkSession
 * @package Toecyd
 */
class UserBookmarkSession extends Model
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
}
