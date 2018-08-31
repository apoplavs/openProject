<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Judge
 * @package Toecyd
 */
class Judge extends Model
{
	
	public $timestamps = false;
	 // The attributes that are mass assignable.
	protected $fillable = [
		'surname', 'name', 'patronymic', 'photo', 'facebook', 'chesnosud', 'status', 'phone', 'rating', 'likes', 'unlikes'
	];

	public static function parseJudgeName(string $judgeNameRaw)
    {
        $matches = [];
        if (preg_match("/головуючий суддя:\s{0,1}(.+);\s{0,1}суддя-доповідач/iu", $judgeNameRaw, $matches))
        {
            $judgeNameRaw = $matches[1];
        }

        $matches = [];
        if (preg_match("/^(\w*) (\w{1})\.\s{0,1}(\w{1})\.$/Uui", $judgeNameRaw, $matches)) {
            // Варіант "Шевченко А.Б."
            return new JudgeNameParsed($matches[1], $matches[2], $matches[3]);
        } elseif (preg_match("/^(\w*) (\w*) (\w*)$/Uui", $judgeNameRaw, $matches)) {
            // Варіант "Шевченко Анатолій Борисович"
            return new JudgeNameParsed($matches[1], mb_substr($matches[2], 0, 1), mb_substr($matches[3], 0, 1));
        } else {
            throw new Exception("Не вдалось розпарсити ім'я судді: '{$judgeNameRaw}'");
        }
    }

    public static function getJudgeIdByParsedName(int $courtCode, JudgeNameParsed $judgeNameParsed)
    {
        $judgeId = Db::table('judges')
            ->select('id')
            ->where('court', '=', $courtCode)
            ->where('surname', 'LIKE', $judgeNameParsed->surname)
            ->where('name', 'LIKE', $judgeNameParsed->name . '%')
            ->where('patronymic', 'LIKE', $judgeNameParsed->patronymic . '%')
            ->value('id');

        if (empty($judgeId)) {
            $judgeId = Db::table('judges')->insertGetId([
                'court'         => $courtCode,
                'surname'       => $judgeNameParsed->surname,
                'name'          => $judgeNameParsed->name,
                'patronymic'    => $judgeNameParsed->patronymic,
            ]);
        }

        return $judgeId;
    }
	
	
	/**
	 * отримати список суддів, враховуючи фільтри, які були задані
	 * @param $regions  array
	 * @param $instances  array
	 * @param $jurisdictions  array
	 * @param $sort_order  integer
	 * @param $search  string
	 * @param $powers_expired  boolean
	 * @return mixed
	 */
	public static function getJudgesList($regions, $instances, $jurisdictions, $sort_order, $search, $powers_expired) {
		
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			// фільтрція за регіоном
			->when(!empty($regions), function ($query) use ($regions) {
				return $query->whereIn('courts.region_code', $regions);
			})
			// фільтрція за інстанцією
			->when(!empty($instances), function ($query) use ($instances) {
				return $query->whereIn('courts.instance_code', $instances);
			})
			// фільтрція за юрисдикцією
			->when(!empty($jurisdictions), function ($query) use ($jurisdictions) {
				return $query->whereIn('courts.jurisdiction', $jurisdictions);
			})
			// якщо не переданий аргумент щоб показувати суддів в яких закінчились повноваження - значить упускємо їх при вибірці
			->when($powers_expired == false, function ($query) {
				return $query->where('judges.status', '!=', 5);
			})
			// якщо застосовано пошук
			->when(!empty($search), function ($query) use ($search) {
				return $query->where('judges.surname', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('judges.surname', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('judges.surname', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('judges.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('judges.rating', 'DESC');
			})
			->paginate(10));
	}
	
	/**
	 * Для не зареєстрованого користувача
	 * отримати список суддів, враховуючи фільтри, які були задані
	 * @param $regions  array
	 * @param $instances  array
	 * @param $jurisdictions  array
	 * @param $sort_order  integer
	 * @param $search  string
	 * @param $powers_expired  boolean
	 * @return mixed
	 */
	public static function getJudgesListGuest($regions, $instances, $jurisdictions, $sort_order, $search, $powers_expired) {
		
		// отримання id користувача
		return (static::select('courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating')
			->join('courts', 'judges.court', '=', 'courts.court_code')
			// фільтрція за регіоном
			->when(!empty($regions), function ($query) use ($regions) {
				return $query->whereIn('courts.region_code', $regions);
			})
			// фільтрція за інстанцією
			->when(!empty($instances), function ($query) use ($instances) {
				return $query->whereIn('courts.instance_code', $instances);
			})
			// фільтрція за юрисдикцією
			->when(!empty($jurisdictions), function ($query) use ($jurisdictions) {
				return $query->whereIn('courts.jurisdiction', $jurisdictions);
			})
			// якщо не переданий аргумент щоб показувати суддів в яких закінчились повноваження - значить упускємо їх при вибірці
			->when($powers_expired == false, function ($query) {
				return $query->where('judges.status', '!=', 5);
			})
			// якщо застосовано пошук
			->when(!empty($search), function ($query) use ($search) {
				return $query->where('judges.surname', 'LIKE', $search.'%');
			})
			// визначення порядку сортування
			->when($sort_order == 1, function ($query) {
				return $query->orderBy('judges.surname', 'ASC');
			})
			->when($sort_order == 2, function ($query) {
				return $query->orderBy('judges.surname', 'DESC');
			})
			->when($sort_order == 3, function ($query) {
				return $query->orderBy('judges.rating', 'ASC');
			})
			->when($sort_order == 4, function ($query) {
				return $query->orderBy('judges.rating', 'DESC');
			})
			->paginate(10));
	}
	
	
	/**
	 * отримує результати автодоповнення
	 * для поля пошуку судді
	 * @param string $search
	 * @return mixed
	 */
	public static function getAutocomplete(string $search) {
		
		$results = static::select('judges.surname', 'judges.name', 'judges.patronymic')
		->where('judges.surname', 'LIKE', $search.'%')
		->orWhere('judges.name', 'LIKE', $search.'%')
		->orWhere('judges.patronymic', 'LIKE', $search.'%')
		->limit(5)
		->get();
		return ($results);
	
	}
	
	
	/**
	 * встановлює новий статус судді
	 * і повертає його
	 * @param $judge_id
	 * @param $status
	 * @param $due_date
	 */
	public static function setNewStatus($judge_id, $status, $due_date) {
		static::where('judges.id', '=', $judge_id)
			->update(['judges.status' => $status,
				'judges.due_date_status' => $due_date]);
		return (static::select('judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'))
			->where('judges.id', '=', $judge_id)
			->first());
	}
	
	
	
	/**
	 * отримати дані по одному судді
	 * @param $judge_id
	 */
	public static function getJudgeData($judge_id) {
		// отримання id користувача
		$user_id = Auth::check() ? Auth::user()->id : 0;
		return (static::select('judges.id', 'judges.surname', 'judges.name',
			'courts.name AS court_name', 'courts.address AS court_address',
			'courts.phone AS court_phone', 'courts.email AS court_email', 'courts.site AS court_site',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('(SELECT COUNT(*) FROM users_likes_judges WHERE users_likes_judges.judge=judges.id) AS likes'),
			DB::raw('(SELECT COUNT(*) FROM users_unlikes_judges WHERE users_unlikes_judges.judge=judges.id) AS unlikes'),
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating', DB::raw('(CASE WHEN user_bookmark_judges.user = '.$user_id.' THEN 1 ELSE 0 END) AS is_bookmark'))
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->leftJoin('user_bookmark_judges', 'judges.id', '=', 'user_bookmark_judges.judge')
			->where('judges.id', '=', $judge_id)
			->first());
	}
	
	
	/**
	 * перевірити чи існує суддя з даним id
	 * @param $id
	 * @return boolean
	 */
	public static function checkJudgeById($id) {
		$judge = static::select('judges.id')
			->where('judges.id', '=', $id)
			->first();
		if (empty($judge)) {
			return (false);
		}
		return (true);
	}
	
}

class JudgeNameParsed
{
    public $surname;
    public $name;
    public $patronymic;

    public function __construct($surname, $name, $patronymic)
    {
        $this->surname = $surname;

        if (mb_strlen($name) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($name, 1));
        }
        $this->name = $name;

        if (mb_strlen($patronymic) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($patronymic, 1));
        }
        $this->patronymic = $patronymic;
    }
}
