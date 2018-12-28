<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JudgeStatus
 * @package Toecyd
 */
class JudgeStatus extends Model
{
	public $timestamps = false;
	
	protected $fillable = ['title'];
}
