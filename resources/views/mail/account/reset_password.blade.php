@extends('mail.account.wrapper')

@section('content')
	Ви зробили запит на скидання паролю.<br>
	Якщо Ви не запитували скидання паролю, будь ласка, ігноруйте це повідомлення.
	<h4>
		<a href="{{ url('/user/password/new') }}?token={{ $token }}">Скинути пароль</a>
	</h4>

@endsection