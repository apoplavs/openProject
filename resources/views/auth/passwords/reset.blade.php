@extends('layouts.app')

@section('content')
	<div class="container mt-5">
		<div class="row">
			<div class="col-lg-6 offset-md-3">
				<div class="card" id="login-form">
					<div class="card-header">
						Відновлення паролю
					</div>
					<div class="card-body">
						<form class="form-group" method="POST" action="{{ route('login') }}">
							{{ csrf_field() }}
							<input type="hidden" name="token" value="{{ $token }}">
							
							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-lg-4 form-control-label">
									E-Mail
								</label>
								<div class="col-lg-12">
									<input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
									
									@if ($errors->has('email'))
										<span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
									@endif
								</div>
							</div>
							
							
							
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label for="password" class="col-lg-4 form-control-label">
									Пароль
								</label>
								<div class="col-lg-12">
									<input id="password" type="password" class="form-control" name="password"
										   required>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-6 offset-md-0">
									<div class="form-check">
										<label>
											<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
											Запамятати мене
										</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-11 offset-md-0">
									<button type="submit" class="btn btn-primary">
										Увійти
									</button>
									<a class="btn btn-link float-right" href="{{ route('password.request') }}">
										Забув пароль?
									</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div> {{-- div .container --}}
	
	
	
	
	
	{{--// поки що невідома сторінка яку потрібно переробити під bootstrap 4--}}
	
	
	
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Reset Password</div>
					
					<div class="panel-body">
						<form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
							{{ csrf_field() }}
							
							<input type="hidden" name="token" value="{{ $token }}">
							
							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-md-4 control-label">E-Mail Address</label>
								
								<div class="col-md-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
									
									@if ($errors->has('email'))
										<span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
									@endif
								</div>
							</div>
							
							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label for="password" class="col-md-4 control-label">Password</label>
								
								<div class="col-md-6">
									<input id="password" type="password" class="form-control" name="password" required>
									
									@if ($errors->has('password'))
										<span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
									@endif
								</div>
							</div>
							
							<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
								<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
								<div class="col-md-6">
									<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
									
									@if ($errors->has('password_confirmation'))
										<span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
									@endif
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary">
										Reset Password
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
