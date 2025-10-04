<!-- filepath: c:\wamp64\www\laravel_jenkins\resources\views\welcome.blade.php -->
@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="row">
    <div class="col-md-12 text-center">
        <div class="jumbotron bg-light p-5 rounded">
            <h1 class="display-4">Welcome to Laravel App</h1>
            <p class="lead">A simple Laravel application with complete authentication system.</p>
            
            @auth
                <div class="alert alert-success">
                    <strong>Hello {{ auth()->user()->name }}!</strong> You are logged in and ready to explore.
                </div>
                <a class="btn btn-primary btn-lg" href="{{ route('dashboard') }}" role="button">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            @else
                <hr class="my-4">
                <p>Please login or register to access the protected areas of the application.</p>
                <a class="btn btn-primary btn-lg me-2" href="{{ route('login') }}" role="button">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a class="btn btn-outline-primary btn-lg" href="{{ route('register') }}" role="button">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            @endauth
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Secure Authentication</h5>
                <p class="card-text">Built with Laravel's robust authentication system including login, registration, and session management with CSRF protection.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                <h5 class="card-title">Protected Routes</h5>
                <p class="card-text">Dashboard and profile pages are protected and only accessible to authenticated users with proper middleware validation.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h5 class="card-title">User Management</h5>
                <p class="card-text">Complete user registration, login, logout functionality with form validation, error handling, and user profile management.</p>
            </div>
        </div>
    </div>
</div>

@auth
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Navigation</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('profile') }}" class="btn btn-outline-info">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth
@endsection