<?php

namespace Toecyd;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CourtSession extends Model
{
    public static function getCourtSessionId($courtCode, $date, $number)
    {
        return DB::table('court_sessions')
            ->select('id')
            ->where('court', '=', $courtCode)
            ->where('date', '=', $date)
            ->where('number', '=', $number)
            ->value('id');
    }
}
