@extends('mail.notifications.wrapper')

@section('content')
	Привіт {{ $data['name'] }},<br>
	повідомляємо Вам, що в судді {{ $data['judge'] }}, що є учасником у справі {{ $data['number'] }},<br>
	змінився статус  "{{ $data['old_status'] }}" --> "<b>{{ $data['new_status'] }}</b>".<br>
	Найближче судове засідання у даній справі призначено на {{ $data['date_session'] }} о {{ $data['time_session'] }}.<br>
	<b>Суд:</b> {{ $data['court_name'] }}<br>
	Перед поїздкою в суд радимо уточнити інформацію.

@endsection