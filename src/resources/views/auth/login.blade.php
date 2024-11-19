@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="login__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>
    <form class="form" action="/login" method="post">
        @csrf

        <div class="form__group-content">
            <div class="form__item">
                <input type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
            </div>
        </div>

        <div class="form__group-content">
            <div class="form__item">
                <input type="password" name="password" placeholder="パスワード" />
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">ログイン</button>
        </div>
    </form>
    <div class="register__link">
        <div class="register__item">
            <p class="register__item-text">
                アカウントをお持ちでない方はこちらから
            </p>
        </div>
        <a class="register__link-text" href="/register">会員登録</a>
    </div>
</div>
@endsection