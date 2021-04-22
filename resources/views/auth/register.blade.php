@extends('layouts.auth')

@section('main-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card w-50 my-5">
            <div class="card-body p-0">
                <div class="p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">{{ __('Register') }}</h1>
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

                    <form method="POST" action="{{ route('store-account') }}" class="user">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="{{ __('Name') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="{{ __('E-Mail Address') }}" required>
                        </div>

                        <div class="form-group">
                            <select name="unit" class="form-control" required>
                                <option selected disabled>Select origin</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="{{ __('Password') }}" required>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-user btn-block" id="register">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>

                    <hr>

                    <div class="text-center">
                        <a class="small" href="{{ route('login') }}">
                            {{ __('Already have an account? Login!') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
