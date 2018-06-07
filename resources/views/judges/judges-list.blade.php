			
	<div class="card-body p-2">
		<hr class="mt-0 mb-1">
		@if(!$judges_list[0])
			<div>За заданими параметрами нічого не знайдено</div>
		@endif
		<div class="container">
			@foreach($judges_list as $judge)
				<div class="row">
					<div class="col-12 col-lg-2 p-0 text-center">
						<img src="{{ $judge->photo }}" alt="фото">
					</div>
					<div class="col-12 col-lg-10 p-0">
						<div class="container">
							<div class="row">
								<div class="col-12 col-lg-6 text-center text-lg-left">
									<h5>
										<a href="{{ url('/judges/'. $judge->id) }}">{{ $judge->surname }}
											@if (strlen($judge->name) > 2)
												{{ $judge->name }} {{ $judge->patronymic }}
												@else
												{{ $judge->name }}. {{ $judge->patronymic }}.
											@endif
										</a>
									</h5>
								</div>
								<div class="col-4 col-lg-2">
									<i class="fa fa-line-chart mx-3" aria-hidden="true" title="рейтинг"> NaN </i>
								</div>
								<div class="col-8 col-lg-4">
									<span class="text-muted">
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
									</span>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-lg-7">
									<div class="float-left court-name">{{ $judge->court_name }}</div>
								</div>
								<div class="col-12 col-lg-5">
									<small class="text-muted ml-1" id="judge{{ $judge->id }}">
										@include('judges.judge-statuses')
									</small>
									{{--if юзер ввійшов - є можливість змінювати статус--}}
									@if(Auth::check())
										<span><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"
												 onclick="setJudgeToChangeStatus({{ $judge->id }})"></i></span>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr class="my-3 my-lg-2">
			@endforeach
			<div class="row">
				<div class="col-12">
					{{ $judges_list->links() }}
				</div>
			</div>
		</div>
	</div>
	
