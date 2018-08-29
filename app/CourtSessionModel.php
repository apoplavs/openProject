<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

class CourtSessionModel extends Model
{
    public static function getCourtSessionId($courtCode, $date, $number)
    {
        return DB::table('auto_assigned_cases')
            ->select('id')
            ->where('court', '=', $courtCode)
            ->where('date', '=', $date)
            ->where('number', '=', $number)
            ->value('id');
    }
}
