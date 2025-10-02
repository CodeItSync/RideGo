<x-guest-layout>
<section class="login-content">
    <div class="container h-100">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="auth-logo">
                            <img src="{{ getSingleMedia(appSettingData('get'),'site_logo',null) }}" class="img-fluid mode light-img rounded-normal" alt="logo">
                            <img src="{{ getSingleMedia(appSettingData('get'),'site_dark_logo',null) }}" class="img-fluid mode dark-img rounded-normal darkmode-logo site_dark_logo_preview" alt="dark-logo">
                        </div>
                        <h2 class="mb-2 text-center">{{ __('message.sign_in') }}</h2>
                        <p class="text-center">{{ __('message.login_with_your_personal_info') }}</p>
                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST" action="{{ route('tracker.login') }}" data-toggle="validator">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label >{{ __('message.email') }}</label>
                                        <input id="email" type="email" name="email"  value="{{old('email')}}"   class="form-control"  placeholder="admin@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label >{{ __('message.password') }}</label>
                                        <input class="form-control" type="password" placeholder="********"  name="password"  required autocomplete="current-password">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- <span>{{ __('message.create_an_account') }} <a href="{{route('auth.register')}}" class="text-primary"> {{ __('message.sign_up') }}</a></span> --}}
                                <button type="submit" class="btn btn-primary">{{ __('message.sign_in') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</x-guest-layout>
