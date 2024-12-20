@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
@if ($errors->any())
<div class="resister_error">
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif

<div class="register__content">
    <div class="register-form__heading">
        <h2>会員登録</h2>
    </div>
    <form class="form" action="/register" method="post">
        @csrf
        <div class="form__group-content">
            <div class="form__item">
                <input type="text" name="name" placeholder="名前" value="{{ old('name') }}" />
            </div>
        </div >

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

        <div class="form__group-content">
            <div class="form__item">
                <input type="password" name="password_confirmation" placeholder="確認用パスワード" />
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">会員登録</button>
        </div>
    </form>
    <div class="login__link">
        <div class="login__item">
            <p class="login__item-text">
                アカウントをお持ちの方はこちらから
            </p>
        </div>
        <a class="login__link-text" href="/login">ログイン</a>
    </div>
</div>
@endsection