<?php

namespace Toecyd\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toecyd\Http\Controllers\Controller;
use Toecyd\UserBookmarkJudge;

/**
 * Управління закладками корисувача
 * Class BookmarksController
 * @package Toecyd\Http\Controllers\User
 */
class BookmarksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Додати суддю в закладки
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addJudgeBookmark(Request $request, $id) {
    	UserBookmarkJudge::createBookmark(Auth::user()->id, $id);
    }

    /**
     * Remove the specified resource from storage.
     * Видалити суддю з закладок
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delJudgeBookmark($id) {
		UserBookmarkJudge::deleteBookmark(Auth::user()->id, $id);
    }
}
