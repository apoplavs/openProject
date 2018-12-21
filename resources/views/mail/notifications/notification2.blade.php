@extends('mail.notifications.wrapper')

@section('content')
	Привіт {{ $data['name'] }},<br>
	повідомляємо Вам, що в судді {{ $data['judge'] }}, якого Ви відстежуєте,<br>
	змінився статус  "{{ $data['old_status'] }}" --> "{{ $data['new_status'] }}". <br>
	Перед поїздкою в суд радимо уточнити інформацію.

@endsection