<?php

namespace Tests\Feature;

use Toecyd\Judge;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class JudgesListTest extends JudgesListGuestTest
{
    public function setUp() {
        parent::setUp();

        $this->url = str_replace('guest/', '', $this->url);
    }

    protected function getFieldstoSelect()
    {
        return Judge::getJudgesListFields($this->user->id);
    }

    public function getJudgesQuery(bool $powers_expired = false, array $orderBy = ['judges.id', 'ASC']) : Builder
    {
        return parent::getJudgesQuery($powers_expired, $orderBy)
            ->leftJoin('user_bookmark_judges', function ($join){
                $join->on('judges.id', '=', 'user_bookmark_judges.judge');
                $join->on('user_bookmark_judges.user', '=', DB::raw($this->user->id));
            });
    }
}
