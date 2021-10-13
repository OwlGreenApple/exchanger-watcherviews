@extends('layouts.auth')

@section('content')
                 @if(session('error'))
                   <div class="alert alert-danger">
                       {{ session('error') }}
                   </div>
                 @endif

                <h4>Halo! Selamat Datang</h4>
                <h6 class="font-weight-light">Masuk untuk melanjutkan.</h6>
                <form class="pt-3" method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Email" name="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">{{ Lang::get('auth.login') }}</button>
                  </div>
                  <div class="my-2 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ Lang::get('auth.remember') }}</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="auth-link text-black">{{ Lang::get('auth.forgot') }}</a>
                  </div>
                  <!--<div class="mb-2">
                    <button type="button" class="btn btn-block btn-facebook auth-form-btn">
                      <i class="mdi mdi-facebook mr-2"></i>Connect using facebook </button>
                  </div>-->
                  <div class="text-center mt-4 font-weight-light">Belum punya akun? <a href="{{url('register')}}" class="text-primary">Buat disini</a>
                  </div>
                </form>
@endsection
