<?php

namespace Toecyd\Http\Controllers\Judges;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Judge;

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
    	$judges_list = Judge::getJudgesList($filters['regions'], $filters['instances'], $filters['sort_order']);
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
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	
	
	// PRIVATE METHODS
	
	
	/**
	 * виконується, якщо застосовувалась фільтрація до списку суддів
	 * @param array $regions
	 * @param array $instances
	 * @param       $rsort
	 */
	private function getFilters() {
		// отримання параметрів, якщо вони були передані
		$regions = Input::has('regions') ? Input::get('regions') : [];
		$instances = Input::has('instances') ? Input::get('instances') : [];
		$rsort = Input::has('rsort') ? intval(Input::get('rsort')) : 0;
		
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
//		$judges_list = Judge::getFilteredJudges($int_regions, $int_instances, $rsort == 'true' ? 'DESC' : 'ASC');
		
		return (['regions'=>$int_regions, 'instances'=>$int_instances, 'sort_order'=>$sort_order]);
 
	}
}
