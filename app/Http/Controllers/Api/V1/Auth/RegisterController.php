<?php

namespace Toecyd\Http\Controllers\Api\V1\Auth;

//use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Toecyd\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Toecyd\User;


class RegisterController extends Controller
{
	
	/*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | Цей контролер обробляє реєстрацію нових користувачів, а також їх
    | перевірка та створення.
    |
    */
	
	use RegistersUsers;
	
	
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}
	
	
	/**
	 * Валідатор для вхідного запиту на реєстрацію
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);
	}
	
	
	
	/**
	 * Створити новий екземпляр користувача після валідної реєстрації.
	 *
	 * @param  array  $data
	 * @return \Toecyd\User
	 *
	 * @SWG\Post(
	 *     path="/api/dashboard",
	 *     description="Зареєструвати нового користувача.",
	 *     operationId="api.dashboard.index",
	 *     produces={"application/json"},
	 *     tags={"Реєстрація"},
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Dashboard overview."
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Unauthorized action.",
	 *     )
	 * )
	 */
	protected function create(array $data)
	{
		//dd($data);
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
		]);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show()
	{
		
		return ('Yes it works');
	}
	
	
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/api/dashboard",
     *     description="Зареєструвати нового користувача.",
     *     operationId="api.dashboard.index",
     *     produces={"application/json"},
     *     tags={"Реєстрація"},
     *     @SWG\Response(
     *         response=200,
     *         description="Dashboard overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function index()
    {
        //
    }
}
