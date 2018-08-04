<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Judge;
use Toecyd\JudgesStatistic;
use Toecyd\UserHistory;
use Toecyd\UsersLikesJudge;
use Toecyd\UsersUnlikesJudge;

/**
 * Class JudgesController
 * @package Toecyd\Http\Controllers\Judges
 */
class JudgesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	// валідація фільтрів
		$request->validate([
			'regions' => 'array',
			'regions.*' => 'numeric|min:2|max:26',
			'instances' => 'array',
			'instances.*' => 'numeric|min:1|max:3',
			'jurisdictions' => 'array',
			'jurisdictions.*' => 'numeric|min:1|max:3',
			'search' => 'string|alpha',
			'sort' => 'numeric|min:1|max:4',
			'expired' => 'numeric'
		]);
		// приведення фільтрів до коректного вигляду
    	$filters = $this->getFilters();
    	// отримання результатів
    	$judges_list = Judge::getJudgesList($filters['regions'], $filters['instances'], $filters['jurisdictions'],
			$filters['sort_order'], $filters['search'], $filters['powers_expired']);
//		$judges_list = DB::table('judges')->paginate(15);
//		$judges_list = Paginator::make($judges_list);
//		$data = $request->session()->all();
//		$data = Auth::user()->id;
//		dd($data);
//		echo $judges_list->links();
		
		return response()->json($judges_list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    	$judge = Judge::getJudgeData($id);
    	$statistic = JudgesStatistic::getStatistic($id);
    	$liked = UsersLikesJudge::isLikedJudge($id);
		$unliked = UsersUnlikesJudge::isUnlikedJudge($id);
		
		// вносим в історію переглядів
		if (Auth::check()) {
		UserHistory::addToHistory($id);
		}
        return (view('judges.judge', compact('judge', 'statistic', 'liked', 'unliked')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
	
	/**
	 * Оновлює статус судді
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return string || \Illuminate\Http\Response
	 */
    public function updateJudgeStatus(Request $request, $id) {
    	$set_status = intval($request->setstatus);
    	$due_date = $request->date;
    	
    	// вілідація форми для зміни статусу
		$validator = Validator::make(['setstatus'=>$set_status, 'date'=>$due_date], [
    		'setstatus' => 'required|integer|between:1,5',
			'date' => 'date|date_format:Y-m-d|after:yesterday|nullable'
		]);
		if ($validator->fails()) {
			return('we check it');
		}
		// отримання статусу судді
		$judge = Judge::setNewStatus($id, $set_status, $due_date);
	
		return view('judges.judge-statuses', compact('judge'));
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	
	/**
	 * автодоповнення в полі пошуку
	 * отримує з GET початок слова
	 * і шукає в БД подібні
	 * повертає результати в json
	 */
	public function autocompleteSearch() {
		$search = Input::has('search') ? Input::get('search') : '';
		// приведення першої букви в верхній регістр
		$search = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
		$autocomplete = Judge::getAutocomplete($search);
		return (json_encode($autocomplete));
	}
	
	
	/**
	 * Поставити лайк судді
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function putLike($id) {
		// перевіряємо чи користувач вже ставив лайк
		$is_liked = UsersLikesJudge::isLikedJudge($id);
		
		// якщо ставив - то прибираємо, в іншому випадку ставимо
		if ($is_liked) {
			$judge_data = UsersLikesJudge::deleteLike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => false
				]));
		} else {
			$judge_data = UsersLikesJudge::putLike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => true,
					'unliked' => false
				]));
		}
	}
	
	/**
	 * Поставити дизлайк судді
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function putUnlike($id) {
		// перевіряємо чи користувач вже ставив дизлайк
		$is_unliked = UsersUnlikesJudge::isUnlikedJudge($id);
		
		// якщо ставив - то прибираємо, в іншому випадку ставимо
		if ($is_unliked) {
			$judge_data = UsersUnlikesJudge::deleteUnlike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => false
				]));
		} else {
			$judge_data = UsersUnlikesJudge::putUnlike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => true
				]));
		}
	}
	
	
	/**
	 * функція для додавання фото судді
	 * ПОКИ ЩО НЕ ВИКОРИСТОВУЄЬСЯ
	 * @param Request $request
	 * @return string
	 */
	public function addPhoto(Request $request) {
		return json_encode(Input::all());
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// PRIVATE METHODS
	
	
	/**
	 * виконується, якщо застосовувалась фільтрація до списку суддів
	 * @return array
	 */
	private function getFilters() {
		// отримання параметрів, якщо вони були передані
		$regions = Input::has('regions') ? Input::get('regions') : [];
		$instances = Input::has('instances') ? Input::get('instances') : [];
		$jurisdictions = Input::has('jurisdictions') ? Input::get('jurisdictions') : [];
		$sort_order = Input::has('sort') ? intval(Input::get('sort')) : 1;
		$search = Input::has('search') ? Input::get('search') : '';
		$powers_expired = Input::has('expired') ? true : false;
		
		// приведення всіх фільтрів до Integer
		$int_regions = [];
		$int_instances = [];
		$int_jurisdictions = [];
		foreach($regions as $region) {
			$int_regions[] = intval($region);
		}
		foreach($instances as $instance) {
			$int_instances[] = intval($instance);
		}
		foreach($jurisdictions as $jurisdiction) {
			$int_jurisdictions[] = intval($jurisdiction);
		}

		return (['regions'=>$int_regions,
			'instances'=>$int_instances,
			'jurisdictions'=>$int_jurisdictions,
			'sort_order'=>$sort_order,
			'search'=>$search,
			'powers_expired'=>$powers_expired]);
	}
}
