@extends('layouts.auth')

@section('title')
    {{ __('Register') }}
@endsection

@section('language-bar')
    <div class="all-select">
        <a href="#" class="monthly-btn">
            <span class="monthly-text">{{__('Change Language')}}</span>
            <select name="language" id="language" class="select-box" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach(Utility::languages() as $language)
                    <option @if($lang == $language) selected @endif value="{{ route('login',$language) }}">{{Str::upper($language)}}</option>
                @endforeach
            </select>
        </a>
    </div>
@endsection

@section('content')
    <div class="login-form">
        <div class="page-title"><h5>{{__('Register')}}</h5></div>
        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
            @csrf
            <div class="form-group">
                <label class="form-control-label">{{ __('Name') }}</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <small>{{ $message }}</small>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="email" class="form-control-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <small>{{ $message }}</small>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password" class="form-control-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <small>{{ $message }}</small>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password-confirm" class="form-control-label">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn-login" tabindex="4">{{ __('Register') }}</button>
            <div class="or-text">{{__('OR')}}</div>
            <div class="text-xs text-muted text-center">
                {{__("Back to")}} <a href="{{route('login',$lang)}}">{{__('Login')}}</a>
            </div>
        </form>
    </div>
@endsection
