@extends('layouts.auth')

@section('title', 'Login | e-Procurement');

@section('main-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card w-50 my-5">
            <div class="card-body p-0">
                <div class="p-5">
                    <div class="text-center">
                        <img class="mb-3" src="{{ url('/img/universitas-pertamina.png') }}" alt="" width="150">
                        <h1 class="h4 text-gray-900 mb-4">e-Procurement</h1>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-left-danger" role="alert">
                            <ul class="pl-4 my-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('post-login') }}" class="user">
                        @csrf
                        <div class="form-group">
                            <input type="email" class="form-control form-control-user" name="email" placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control form-control-user" name="password" placeholder="{{ __('Password') }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>

                    @if (Route::has('register'))
                        <div class="text-center">
                            <a class="small" href="{{ route('register') }}">{{ __('Create an Account!') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
