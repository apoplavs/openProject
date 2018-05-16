@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					{{--<div class="panel-heading">Dashboard</div>--}}
					{{----}}
					{{--<div class="panel-body">--}}
						{{--@if (session('status'))--}}
							{{--<div class="alert alert-success">--}}
								{{--{{ session('status') }}--}}
							{{--</div>--}}
						{{--@endif--}}
						{{----}}
						{{--You are logged in!--}}
					{{--</div>--}}
				</div>
			</div>
		</div>
	</div>
	
	<!-- Search -->
	
	<div class="container mt-5">
		<div class="row">
			<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
				<form method="post">
					<div class="form-row">
						<div class="col-12 col-md-9 mb-2 mb-md-0">
							<input type="text" class="form-control form-control" placeholder="Пошук...">
						</div>
						<div class="col-12 col-md-3">
							<button type="submit" class="btn btn-block btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Filters -->
	
	<div class="container ml-1" id="filters">
		<div class="row">
			<div class="col-lg-3">
				<div class="card my-4">
					<h4 class="card-header"><i class="fa fa-filter" aria-hidden="true"></i> Фільтри</h4>
					<div class="card-body">
						<form method="get" id="form-filters" name="filters">
							
							<div class="row">
								<div class="col-lg-12">
									<h6>Інстанція</h6>
									<ul class="list-unstyled mb-0">
										<li><label><input type="checkbox" value="3" name="instance"><span class="checkmark"></span> Перша</label></li>
										<li><label><input type="checkbox" value="2" name="instance"><span class="checkmark"></span> Апеляційна</label></li>
										<li><label><input type="checkbox" value="1" name="instance"><span class="checkmark"></span> Касаційна</label></li>
									</ul>
								</div>
							</div>
							
							<hr>
							
							<div class="row">
								<div class="col-lg-12">
									<h6>Регіон суду</h6>
									<ul class="list-unstyled mb-0">
										<li><label><input type="checkbox" value="2" name="region"><span class="checkmark"></span> Вінницька область</label></li>
										<li><label><input type="checkbox" value="3" name="region"><span class="checkmark"></span> Волинська область</label></li>
										<li><label><input type="checkbox" value="4" name="region"><span class="checkmark"></span> Дніпропетровська область</label></li>
										<li><label><input type="checkbox" value="5" name="region"><span class="checkmark"></span> Донецька область</label></li>
										<li><label><input type="checkbox" value="6" name="region"><span class="checkmark"></span> Житомирська область</label></li>
										<li><label><input type="checkbox" value="7" name="region"><span class="checkmark"></span> Закарпатська область</label></li>
										<li><label><input type="checkbox" value="8" name="region"><span class="checkmark"></span> Запорізька область</label></li>
										<li><label><input type="checkbox" value="9" name="region"><span class="checkmark"></span> Івано-Франківська область</label></li>
										<li><label><input type="checkbox" value="10" name="region"><span class="checkmark"></span> Київська область</label></li>
										<li><label><input type="checkbox" value="11" name="region"><span class="checkmark"></span> Кіровоградська область</label></li>
										<li><label><input type="checkbox" value="12" name="region"><span class="checkmark"></span> Луганська область</label></li>
										<li><label><input type="checkbox" value="13" name="region"><span class="checkmark"></span> Львівська область</label></li>
										<li><label><input type="checkbox" value="14" name="region"><span class="checkmark"></span> Миколаївська область</label></li>
										<li><label><input type="checkbox" value="15" name="region"><span class="checkmark"></span> Одеська область</label></li>
										<li><label><input type="checkbox" value="16" name="region"><span class="checkmark"></span> Полтавська область</label></li>
										<li><label><input type="checkbox" value="17" name="region"><span class="checkmark"></span> Рівненська область</label></li>
										<li><label><input type="checkbox" value="18" name="region"><span class="checkmark"></span> Сумська область</label></li>
										<li><label><input type="checkbox" value="19" name="region"><span class="checkmark"></span> Тернопільська область</label></li>
										<li><label><input type="checkbox" value="20" name="region"><span class="checkmark"></span> Харківська область</label></li>
										<li><label><input type="checkbox" value="21" name="region"><span class="checkmark"></span> Херсонська область</label></li>
										<li><label><input type="checkbox" value="22" name="region"><span class="checkmark"></span> Хмельницька область</label></li>
										<li><label><input type="checkbox" value="23" name="region"><span class="checkmark"></span> Черкаська область</label></li>
										<li><label><input type="checkbox" value="24" name="region"><span class="checkmark"></span> Чернівецька область</label></li>
										<li><label><input type="checkbox" value="25" name="region"><span class="checkmark"></span> Чернігівська область</label></li>
										<li><label><input type="checkbox" value="26" name="region"><span class="checkmark"></span> м. Київ</label></li>
									</ul>
								</div>
							</div>
							
							<hr>
							
							<div class="row">
								<div class="col-lg-6">
									<button type="reset" class="btn btn-outline-info">Скинути</button>
								</div>
								<div class="col-lg-6">
									<button type="button" onclick="getJudgesList()" class="btn btn-primary">Показати</button>
								</div>
							</div>
						</form>
					</div>
				</div> <!-- Card -->
			</div> <!-- Filters -->
			
			
			<!-- Main list -->
			<div class="col-lg-9">
				<div class="card card-outline-secondary my-4">
					<div class="card-header">
						Список суддів <span class="ml-5"> сортувати: <label id="sorting-type">
								<input type="checkbox" onchange="changeSorting(this.checked)" form="form-filters" name="sorting">
								<i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
								
							</label></span>
					</div>
					<div  id="judges-list">
						{{--@include('judges.judges-list')--}}
					</div>
					
				</div><!-- /.card -->
			</div> <!-- col-lg-9 -->
			
			
		</div> <!-- row -->
	</div> <!-- container -->

@endsection
