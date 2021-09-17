@extends('layouts.auth')

@section('content')
                <h4>{{ __('Reset Password') }}</h4>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <h6 class="font-weight-light">You can reset your password here.</h6>
                <form class="pt-3" method="POST" action="{{ route('password.email') }}">
                  @csrf
                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">{{ __('Send Password Reset Link') }}</button>
                  </div>
                  <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="{{url('register')}}" class="text-primary">Create</a>
                  </div>
                </form>
@endsection
