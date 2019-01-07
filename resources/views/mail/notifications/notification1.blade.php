@extends('mail.notifications.wrapper')

@section('content')
	<b>Привіт {{ $data['name'] }}</b>,<br>
	повідомляємо Вам, що в судді {{ $data['judge'] }}, якого Ви відстежуєте,<br>
	змінився статус  "{{ $data['old_status'] }}" --> "<b>{{ $data['new_status'] }}</b>". <br>
	Перед поїздкою в суд радимо уточнити інформацію.

@endsection