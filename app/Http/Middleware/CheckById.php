<?php

namespace Toecyd\Http\Middleware;

use Closure;
use Exception;
use Toecyd\Judge;

/**
 * Перевіряє чи існує в БД об'єкт із заданим id
 * Class CheckById
 * @package Toecyd\Http\Middleware
 */
class CheckById
{
	// id об'єкта, що перевіряється
	private $id;
	
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 * @throws Exception
	 */
    public function handle($request, Closure $next, $guard) {
    	$this->id = $request->route('id');
    	
    	// визначаємо, який об'єкт потрібно перевірити
		switch ($guard) {
			case 'judge':
				return $this->checkJudgeById($request, $next);
			case 'court':
				return $this->checkCourtById($request, $next);
		}
		// якщо передана невалідна назва об'єкту
		throw new Exception("Unknown guard: " . $guard);
    }
	
	
	
	/**
	 * Перевіряє, чи існує суддя з таким id в БД
	 * @return mixed
	 */
	private function checkJudgeById($request, Closure $next) {
		
		if (!Judge::checkJudgeById($this->id)) {
			return response()->json([
				'message' => 'Неіснуючий id судді!'
			], 422);
		}
		
		return $next($request);
	}
	
	
	/**
	 * Перевіряє, чи існує в БД суд з таким id
	 * @return mixed
	 */
	private function checkCourtById($request, Closure $next) {
		
		return $next($request);
	}
}
