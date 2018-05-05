@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header">
                        Відновлення паролю
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
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
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
									@if ($errors->has('email'))
										<span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
									@endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-lg-11 offset-md-1">
                                    <button type="submit" class="btn btn-primary">
                                        Надіслати посилання для скидання паролю
                                    </button>
                                </div>
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- div .container --}}
@endsection
