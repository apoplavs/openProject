@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header">
                        Вхід
						@if ($errors->has('email'))
							<span class="help-block float-right">
                             	<strong>{{ $errors->first('email') }}</strong>
                            </span>
						@elseif ($errors->has('password'))
							<span class="help-block float-right">
                             	<strong>{{ $errors->first('password') }}</strong>
                            </span>
						@endif
                    </div>
					
                    <div class="card-body">
						
                        <form class="form-group" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-lg-4 form-control-label">
                                    E-Mail
                                </label>
                                <div class="col-lg-12">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                           required autofocus>
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
@endsection
