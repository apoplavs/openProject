<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

/**
 * Отримання суддівської статистики
 * Class JudgesStatistic
 * @package Toecyd
 */
class JudgesStatistic extends Model
{
	public $timestamps = false;
	
	
	/**
	 * отримує з БД і повертає статистику по судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getStatistic($judge) {
		return (static::select('civil_amount', 'criminal_amount', 'adminoffence_amount', 'admin_amount', 'commercial_amount')
		->where('judge', '=', $judge)
		->first());
	}
 
}
