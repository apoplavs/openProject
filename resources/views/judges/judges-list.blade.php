			
					<div class="card-body p-2">
						<hr class="mt-0">
						@foreach($judges_list as $judge)
							{{--judge photo зробити значення по дефолту в БД і прибрати цей if звідси--}}
							<p>
								<img id="judge-photo" src="
								@if($judge->photo)
									{{ $judge->photo }}
								@else
									http://placehold.it/90x90
								@endif" alt="фото" class="float-left mr-3">
								<h5>
									<a href="#">{{ $judge->surname }} {{ $judge->name }}. {{ $judge->patronymic }}.</a>
									<small class="text-muted ml-5">
										<i class="fa fa-line-chart" aria-hidden="true"> NaN </i>
										<small class="text-muted float-right mr-5">
											@if($judge->is_bookmark == 1)
												відстежується <i class="fa fa-bookmark" aria-hidden="true"></i>
											@else
												відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i>
											@endif
										</small>
									</small>
								</h5>
								<div id="court-name" class="float-left">{{ $judge->court_name }}</div>
								<small class="text-muted ml-1">
									@switch($judge->status)
										@case(1)
										<i class="fa fa-briefcase" aria-hidden="true"></i> на роботі {{ $judge->updated_status }}
											@break
										@case(2)
										<i class="fa fa-medkit" aria-hidden="true"></i> на лікарняному з {{ $judge->updated_status }}
											@break
										@case(3)
										<i class="fa fa-calendar-check-o" aria-hidden="true"></i> у відпустці з {{ $judge->updated_status }}
											@break
										@case(4)
										<i class="fa fa-calendar-check-o" aria-hidden="true"></i> відсутній на робочому місці з {{ $judge->updated_status }}
											@break
										@case(5)
										<i class="fa fa-calendar-check-o" aria-hidden="true"></i> припиено повноваження з {{ $judge->updated_status }}
											@break
									@endswitch
									{{--if юзер ввійшов - є можливість змінювати статус--}}
								</small>
							</p>
							<br>
							<hr>
						@endforeach
						
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Бандура Олена Петрівна</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Млинівський районний суд Рівненської області <small class="text-muted ml-5"><i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 03/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Кислашко В. Д.</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Млинівський районний суд Рівненської області <small class="text-muted ml-5"><i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 03/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Бандура Алла Петрівна</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Млинівський районний суд Рівненської області <small class="text-muted ml-5"><i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 03/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Степанюк О. Г.</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежується <i class="fa fa-bookmark" aria-hidden="true"></i></small></small></h5>--}}
						{{--Іванківський районний суд Київської області <small class="text-muted ml-5"><i class="fa fa-medkit" aria-hidden="true"></i> на лікарняному з 19/03/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Бачинський Остап Семенович</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Млинівський районний суд Рівненської області <small class="text-muted ml-5"><i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 03/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Бандура Олена Петрівна</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Млинівський районний суд Рівненської області <small class="text-muted ml-5"><i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 03/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<p><img src="http://placehold.it/90x90" alt="фото" class="float-left mr-3"><h5><a href="#">Криворучко Віталій Сергійович</a> <small class="text-muted ml-5"><i class="fa fa-line-chart" aria-hidden="true"> NaN </i></i><small class="text-muted float-right mr-5">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></small></small></h5>--}}
						{{--Березанський міський суд Київської області <small class="text-muted ml-5"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> у відпустці з 01/04/18</small></p><br>--}}
						{{--<hr>--}}
						{{--<nav aria-label="Page navigation example">--}}
						{{ $judges_list->links() }}
					</div>
				
