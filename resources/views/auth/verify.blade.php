@extends('layouts.auth')

@section('content')
                <h4>{{ __('Verify Your Email Address') }}</h4>
                <h6 class="font-weight-light">{{ __('Before proceeding, please check your email for a verification link.') }} <br>
                {{ __('If you did not receive the email') }},
                </h6>
                <form class="pt-3" method="POST" action="{{ route('verification.resend') }}">
                  @if (session('resent'))
                      <div class="alert alert-success" role="alert">
                          {{ __('A fresh verification link has been sent to your email address.') }}
                      </div>
                  @endif
                  @csrf
                  <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">{{ __('click here to request another') }}</button>
                  </div>
                </form>
@endsection
