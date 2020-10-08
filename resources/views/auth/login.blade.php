@extends('layouts.auth')
@section('content')
<div class="login-box">
    <!--div class="login-logo">
        <a href="{{ route('admin.home') }}">
            {{ trans('panel.site_title') }}
        </a>
    </div-->
    <div class="login-box-body">
    	<h5 style="text-align: center; padding: 0px 20px 20px 20px; font-size: 25px;">{{ trans('global.login') }}</h5>

        @if(session('message'))
            <p class="alert alert-info">
                {{ session('message') }}
            </p>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <input id="email" type="email" name="email" class="form-control" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}" value="{{ old('email', null) }}" data-validation="required email">

                @if($errors->has('email'))
                    <p class="help-block">
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" name="password" class="form-control" required placeholder="{{ trans('global.login_password') }}" data-validation="required">

                @if($errors->has('password'))
                    <p class="help-block">
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label><input type="checkbox" name="remember"> {{ trans('global.remember_me') }}</label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">
                        {{ trans('global.login') }}
                    </button>
                </div>
            </div>
        </form>

        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}">
                {{ trans('global.forgot_password') }}
            </a><br>
        @endif


    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
    	$.validate();
    /*$('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' 
    });*/
  });
</script>
@endsection
