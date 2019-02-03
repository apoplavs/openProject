<?php

namespace Toecyd\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Toecyd\UserBookmarkSession;

/**
 * Перевірка чи має поточний користувач доступ до
 * читання/зміни/видалення ресурсу з заданим id
 * наприклад доступ до закладки може мати тільки користувач
 * який зробив цю закладку
 * Class CheckAccess
 * @package Toecyd\Http\Middleware
 */
class CheckAccess
{
	// id об'єкта, що перевіряється
	private $id;
	
	// id поточного користувача
	private $user_id;
	
	
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
		$this->user_id = Auth::user()->id;
		
		// визначаємо, який об'єкт потрібно перевірити
		switch ($guard) {
			case 'session-bookmark':
				return $this->checkSessionBookmark($request, $next);
		}
		// якщо передана невалідна назва об'єкту
		throw new Exception("Unknown guard: " . $guard);
    }
	
	/**
	 * Перевіряє, чи має поточний користувач доступ до закладки на судове засідання
	 * з таким id судового засідання
	 * @return mixed
	 */
	private function checkSessionBookmark($request, Closure $next) {
		
		if (!UserBookmarkSession::checkAccessToBookmark($this->id, $this->user_id)) {
			return response()->json([
				'message' => 'Заборонено!'
			], 403);
		}
		
		return $next($request);
	}
}
