<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Отримання суддівської статистики
 * Class JudgesStatistic
 * @package Toecyd
 */
class JudgesStatistic extends Model
{
	public $timestamps = false;
	
	
	/**
	 * отримує з БД і повертає загальну статистику по судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getCommonStatistic($judge) {
		$amount = DB::table('judges_adminoffence_statistic')
			->select(DB::raw('judges_adminoffence_statistic.amount AS adminoffence_amount '),
				DB::raw('judges_admin_statistic.amount AS admin_amount '),
				DB::raw('judges_civil_statistic.amount AS civil_amount '),
				DB::raw('judges_criminal_statistic.amount AS criminal_amount '),
				DB::raw('judges_commercial_statistic.amount AS commercial_amount '))
			->join('judges_admin_statistic', 'judges_admin_statistic.judge', '=', $judge)
			->join('judges_civil_statistic', 'judges_civil_statistic.judge', '=', $judge)
			->join('judges_criminal_statistic', 'judges_criminal_statistic.judge', '=', $judge)
			->join('judges_commercial_statistic', 'judges_commercial_statistic.judge', '=', $judge)
			->where('judge', '=', $judge)
			->get();
		return ($amount);
	}
	
	
	/**
	 * отримує з БД і повертає статистику по КУпАП судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getAdminoffenceStatistic($judge) {
		$statistic = DB::table('judges_adminoffence_statistic')
			->select('amount', 'cases_on_time', 'cases_not_on_time', 'average_duration', 'positive_judgment',
				'negative_judgment', 'was_appeal', 'approved_by_appeal', 'not_approved_by_appeal')
			->where('judge', '=', $judge)
			->first();
		$result = [];
		$result['amount'] = $statistic->amount;
		
		$has_cases = $statistic->cases_on_time + $statistic->cases_not_on_time;
		// якщо достатньо справ, щоб порахувти статистику
		if ($has_cases > 10) {
			$result['cases_on_time'] = intval(($statistic->cases_on_time / $has_cases) * 100);
			$result['cases_not_on_time'] = intval(($statistic->cases_not_on_time / $has_cases) * 100);
		}
		
		$result['average_duration'] = $statistic->average_duration;
		
		$has_result_in_appeal = $statistic->approved_by_appeal + $statistic->not_approved_by_appeal;
		if ($has_result_in_appeal > 10) {
			$result['approved_by_appeal'] = intval(($statistic->approved_by_appeal / $has_result_in_appeal) * 100);
			$result['not_approved_by_appeal'] = intval(($statistic->not_approved_by_appeal / $has_result_in_appeal) * 100);
		}
		
		return ($result);
	}
	
	/**
	 * отримує з БД і повертає статистику по цивільних справах судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getCivilStatistic($judge) {
		$statistic = DB::table('judges_civil_statistic')
			->select('amount', 'cases_on_time', 'cases_not_on_time', 'average_duration', 'positive_judgment',
				'negative_judgment', 'other_judgment', 'was_appeal', 'approved_by_appeal', 'not_approved_by_appeal')
			->where('judge', '=', $judge)
			->first();
		$result = [];
		$result['amount'] = $statistic->amount;
		
		$has_cases = $statistic->cases_on_time + $statistic->cases_not_on_time;
		// якщо достатньо справ, щоб порахувти статистику
		if ($has_cases > 10) {
			$result['cases_on_time'] = intval(($statistic->cases_on_time / $has_cases) * 100);
			$result['cases_not_on_time'] = intval(($statistic->cases_not_on_time / $has_cases) * 100);
		}
		$result['average_duration'] = $statistic->average_duration;
		
		$has_result_in_appeal = $statistic->approved_by_appeal + $statistic->not_approved_by_appeal;
		if ($has_result_in_appeal > 10) {
			$result['approved_by_appeal'] = intval(($statistic->approved_by_appeal / $has_result_in_appeal) * 100);
			$result['not_approved_by_appeal'] = intval(($statistic->not_approved_by_appeal / $has_result_in_appeal) * 100);
		}
		return ($result);
	}
	
	/**
	 * отримує з БД і повертає статистику по кимінальних провадженнях судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getCriminalStatistic($judge) {
		$statistic = DB::table('judges_criminal_statistic')
			->select('amount', 'cases_on_time', 'cases_not_on_time', 'average_duration', 'positive_judgment',
				'negative_judgment', 'was_appeal', 'approved_by_appeal', 'not_approved_by_appeal')
			->where('judge', '=', $judge)
			->first();
		$result = [];
		$result['amount'] = $statistic->amount;
		
		$has_cases = $statistic->cases_on_time + $statistic->cases_not_on_time;
		
		// якщо достатньо справ, щоб порахувти статистику
		if ($has_cases > 10) {
			$result['cases_on_time'] = intval(($statistic->cases_on_time / $has_cases) * 100);
			$result['cases_not_on_time'] = intval(($statistic->cases_not_on_time / $has_cases) * 100);
		}
		
		$result['average_duration'] = $statistic->average_duration;
		
		// якщо буди справи в апелції
		$has_result_in_appeal = $statistic->approved_by_appeal + $statistic->not_approved_by_appeal;
		if ($has_result_in_appeal > 10) {
			$result['approved_by_appeal'] = intval(($statistic->approved_by_appeal / $has_result_in_appeal) * 100);
			$result['not_approved_by_appeal'] = intval(($statistic->not_approved_by_appeal / $has_result_in_appeal) * 100);
		}
		
		return ($result);
	}
	
	
	
	
	
	
	/**
	 * отримує з БД і повертає статистику по Господарських справах судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getCommercialStatistic($judge) {
		$statistic = DB::table('judges_commercial_statistic')
			->select('amount')
			->where('judge', '=', $judge)
			->first();
		$result = [];
		$result['amount'] = $statistic->amount;
		return ($result);
	}
	
	
	/**
	 * отримує з БД і повертає статистику по Адміністративних справах судді
	 * id якого передано в параметрах
	 * використовується на сторінці судді
	 * @param $judge
	 */
	public static function getAdminStatistic($judge) {
		$statistic = DB::table('judges_admin_statistic')
			->select('amount')
			->where('judge', '=', $judge)
			->first();
		$result = [];
		$result['amount'] = $statistic->amount;
		return ($result);
	}
 
}
