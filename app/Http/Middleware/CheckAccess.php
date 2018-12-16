<?php

namespace Toecyd\Http\Middleware;

use Closure;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public function handle($request, Closure $next, $guard) {
		$this->id = $request->route('id');
        return $next($request);
    }
}
