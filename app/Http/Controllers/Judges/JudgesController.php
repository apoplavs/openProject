<?php

namespace Toecyd\Http\Controllers\Judges;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Judge;
use Toecyd\JudgesStatistic;

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
//    	$logged = Auth::check();
    	
    	$filters = $this->getFilters();

//		if (Input::has('regions') || Input::has('instances') || Input::has('rsort')) {
//			return ($this->filteredJudges(Input::get('regions'), Input::get('instances'), Input::get('rsort')));
//		}
//    	if (array_key_exists('rsort', $all_input) && $all_input['rsort'] == 'false') {
//    	return ($all_input);
//		}
//		return ([$regions, empty($instances), $rsort]);
  
//    	dd($logged);
//    	$regions = Input::get('regions');
//    	$instances = Input::get('instances');
//    	print_r($regions);
//		print_r($instances);
//    	dd($regions, $instance);
    	$judges_list = Judge::getJudgesList($filters['regions'], $filters['instances'],
			$filters['sort_order'], $filters['search']);
//		$judges_list = DB::table('judges')->paginate(15);
//		$judges_list = Paginator::make($judges_list);
//		$data = $request->session()->all();
//		$data = Auth::user()->id;
//		dd($data);
//		echo $judges_list->links();
//		dd($judges_list[0]);
		
		return view('judges.judges-list', compact('judges_list'));
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
        return (view('judges.judge', compact('judge', 'statistic')));
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
	 * Put like for judge.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function putLike($id)
	{
		$judge = Judge::getJudgeData($id);
		$statistic = JudgesStatistic::getStatistic($id);
		return (view('judges.judge', compact('judge', 'statistic')));
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
		$rsort = Input::has('rsort') ? intval(Input::get('rsort')) : 0;
		$search = Input::has('search') ? Input::get('search') : '';

		// визначення порядку сортування
		$sort_order = $rsort ? 'DESC' : 'ASC';
		
		$int_regions = [];
		$int_instances = [];
		foreach($regions as $region) {
			$int_regions[] = intval($region);
		}
		foreach($instances as $instance) {
			$int_instances[] = intval($instance);
		}

		return (['regions'=>$int_regions, 'instances'=>$int_instances, 'sort_order'=>$sort_order, 'search'=>$search]);
	}
}
