<?php

namespace Toecyd;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EssencesCases extends Model
{
    protected $fillable = ['title'];

    public static function parseTitle(string $titleRaw)
    {
        $result = '';

        if (mb_strlen($titleRaw) < 5) {
            return $result;
        }

        $titleRaw = mb_strtolower($titleRaw);

        $matches = [];
        if (preg_match("/.+ (про .+)/ui", $titleRaw, $matches)) {
            $titleRaw = $matches[1];
        }

        if (mb_strlen($titleRaw) >= 255) {
            return $result;
        }

        if (preg_match("/ \w. \?\w./u", $titleRaw) || preg_match("/\d\d+/u", $titleRaw)) {
            return $result;
        }

        // Видаляємо небуквенні символи з кінця рядка, потрібно щоб рядок закінчувався на "\w$"
        $titleRaw = preg_replace("/\W{0,}$/u", '', $titleRaw);

        $result = $titleRaw;
        return $result;
    }

    public static function fillIdByParsedTitle(string $titleParsed)
    {
        $titleId = Db::table('essences_cases')
            ->select('id')
            ->where('title', '=', $titleParsed)
            ->value('id');

        if (empty($titleId)) {
            $titleId = Db::table('essences_cases')->insertGetId([
                'title' => $titleParsed,
            ]);
        }
        return $titleId;
    }
}
