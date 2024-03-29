@extends('layouts.auth')

@section('content')
                <h4>{{ __('Konfirmasi Password') }}</h4>
                <h6 class="font-weight-light">{{ __('Mohon konfirmasi password dari anda sebelum melanjutkan.') }}</h6>
                <form class="pt-3" method="POST" action="{{ route('password.confirm') }}">
                  @csrf
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" >
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">{{ __('Konfirmasi Password') }}</button>
                  </div>
                  <div class="text-center mt-4 font-weight-light"><a href="route('password.request')" class="text-primary">{{ __('Lupa Password?') }}</a>
                  </div>
                </form>
@endsection
