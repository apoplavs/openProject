@extends('mail.notifications.wrapper')

@section('content')
	<b>Привіт {{ $data['name'] }}</b>,<br>
	у справі <b>№ {{ $data['number'] }}</b>, яку Ви відстежуєте,<br>
	з'явилась нова дата судового засідання - <b>{{ $data['date_session'] }} о {{ $data['time_session'] }}</b>.<br>
	<br>
	<a href="{{ url('/user-profile') }}">Перейти до особстого кабінету</a>
	<br>
	@if(strlen($data['note']) > 5)
		Примітки:
		<pre>
			{{ $data['note'] }}
		</pre>
	@endif

@endsection