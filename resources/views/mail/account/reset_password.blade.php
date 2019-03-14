@extends('mail.account.wrapper')

@section('content')
	Ви зробили запит на скидання паролю.<br>
	Якщо Ви не запитували скидання паролю, будь ласка, ігноруйте це повідомлення.
	<h4>
		<a href="{{ url('/recover-password') }}?token={{ $data['token'] }}">Скинути пароль</a>
	</h4>

@endsection