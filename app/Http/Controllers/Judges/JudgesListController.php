<?php

namespace Toecyd\Http\Controllers\Judges;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Judge;

class JudgesListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$logged = Auth::check();
//    	dd($logged);
    	$regions = Input::get('region');
    	$instance = Input::get('instance');
//    	print_r($regions);
//    	dd($regions, $instance);
    	$judges_list = Judge::getAllJudges(Input::get('sorting'));
//		$judges_list = DB::table('judges')->paginate(15);
//		$judges_list = Paginator::make($judges_list);
//		$data = $request->session()->all();
		$data = Auth::user()->id;
//		dd($data);
//		echo $judges_list->links();
//		dd($judges_list[0]);
		return view('judges.judges', compact('judges_list'));
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
}
