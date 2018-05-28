			
					<div class="card-body p-2">
						<hr class="mt-0 mb-1">
						@if(!$judges_list[0])
							<div>За заданими параметрами нічого не знайдено</div>
						@endif
						@foreach($judges_list as $judge)
							<p>
								<img src="{{ $judge->photo }}" alt="фото" class="float-left mr-3">
								<h5>
									<a href="{{ url('/judges/'. $judge->id) }}">{{ $judge->surname }}
										@if (strlen($judge->name) > 2)
											{{ $judge->name }} {{ $judge->patronymic }}
											@else
											{{ $judge->name }}. {{ $judge->patronymic }}.
										@endif
									</a>
									<small class="text-muted">
										<i class="fa fa-line-chart mx-3" aria-hidden="true" title="рейтинг"> NaN </i>
										@if(Auth::check())
										<small class="text-muted float-right mr-3 bookmark" onclick="addBookmark(this, {{ $judge->id }})">
											{{--<input type="hidden" value="{{ $judge->id }}">--}}
											@if($judge->is_bookmark == 1)
												<span>відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i>
											@else
												<span>відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
											@endif
										</small>
										@endif
									</small>
								</h5>
								<div class="float-left court-name">{{ $judge->court_name }}</div>
								<small class="text-muted ml-1" id="judge{{ $judge->id }}">
									@include('judges.judge-statuses')
								</small>
								{{--if юзер ввійшов - є можливість змінювати статус--}}
								@if(Auth::check())
								<span><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"
										 onclick="setJudgeToChangeStatus({{ $judge->id }})"></i></span>
								@endif
							</p>
							<br>
							<hr class="my-1">
						@endforeach
						{{--<nav aria-label="Page navigation example">--}}
						{{ $judges_list->links() }}
					</div>
				
