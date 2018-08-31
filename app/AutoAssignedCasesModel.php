<?php

namespace Toecyd;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AutoAssignedCasesModel extends Model
{
    public static function getCaseId($courtCode, $dateRegistration, $number)
    {
        return DB::table('auto_assigned_cases')
            ->select('id')
            ->where('court', '=', $courtCode)
            ->where('date_registration', '=', $dateRegistration)
            ->where('number', '=', $number)
            ->value('id');
    }
}
