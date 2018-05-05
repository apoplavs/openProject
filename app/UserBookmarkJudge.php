<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

class UserBookmarkJudge extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'judge'
	];
}
