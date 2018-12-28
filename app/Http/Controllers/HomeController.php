<?php

namespace Toecyd\Http\Controllers;

use Illuminate\Http\Request;
use Toecyd\UserBookmarkJudge;
use Toecyd\UserHistory;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * показати домашню сторінку користувача
	 * (потрібно буде переробити, щоб показувати кожен розділ окремо)
     * @return \Illuminate\Http\Response
     */
    public function index() {
		$judges_list = UserBookmarkJudge::getBookmarkJudges();
		$judges_history = UserHistory::getHistoryJudges();
        return view('home', compact('judges_list', 'judges_history'));
    }
}
