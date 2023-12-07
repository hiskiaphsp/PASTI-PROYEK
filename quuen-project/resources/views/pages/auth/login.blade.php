<!-- resources/views/auth/login.blade.php -->

<x-auth-layout title="Login">
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12">
                <div class="login-card">
                    <div>
                        <div class="login-main">
                            <form class="theme-form" method="post" action="{{ route('auth.login') }}">
                                <h4>Sign in to account</h4>
                                @csrf
                                <p>Enter your email & password to login</p>
                                <div class="form-group">
                                    <label class="col-form-label" for="email">Email Address</label>
                                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" autofocus placeholder="Test@gmail.com">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="password">Password</label>
                                    <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="*********">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <a href="#">Forgot Password?</a>
                                </div>
                                <div class="form-group mt-20">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                                    </div>
                                </div>
                                <p class="mt-4 mb-0">Don't have an account? <a class="ms-2" href="#">Create Account</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
