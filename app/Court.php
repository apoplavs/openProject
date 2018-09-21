<?php

namespace Toecyd;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
	public $timestamps = false;
	// The attributes that are mass assignable.
	protected $fillable = [
		'address', 'phone', 'email', 'site'
	];

	public static function getCourtCodes()
    {
        return DB::table('courts')
            ->select('court_code')
            ->whereNotIn('region_code', [1, 5, 12]) //відкидаємо АР Крим, Донецьку, Луганську області
            ->where('court_code', '<', 2800) // відкидаємо спеціалізовані суди
            ->pluck('court_code');
    }
}
