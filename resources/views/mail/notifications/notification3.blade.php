@extends('mail.notifications.wrapper')

@section('content')
	<b>Привіт {{ $data['name'] }}</b>,<br>
	повідомляємо Вам, що в судді {{ $data['judge'] }}, що є учасником у справі <b>№ {{ $data['number'] }}</b>,<br>
	змінився статус  "{{ $data['old_status'] }}" --> "<b>{{ $data['new_status'] }}</b>".<br>
	Найближче судове засідання у даній справі призначено на <b>{{ $data['date_session'] }} о {{ $data['time_session'] }}</b>.<br>
	<b>Суд:</b> {{ $data['court_name'] }}<br>
	Перед поїздкою в суд радимо уточнити інформацію.

@endsection