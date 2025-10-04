<!-- filepath: c:\wamp64\www\laravel_jenkins\resources\views\dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Welcome back, <strong>{{ auth()->user()->name }}</strong>! You are successfully logged in.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Your Account Information</h5>
                        <div class="list-group">
                            <div class="list-group-item">
                                <strong>Name:</strong> {{ auth()->user()->name }}
                            </div>
                            <div class="list-group-item">
                                <strong>Email:</strong> {{ auth()->user()->email }}
                            </div>
                            <div class="list-group-item">
                                <strong>Member since:</strong> {{ auth()->user()->created_at->format('M d, Y') }}
                            </div>
                            <div class="list-group-item">
                                <strong>Last updated:</strong> {{ auth()->user()->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user"></i> View Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Total Users</h5>
                <h2 class="text-primary">{{ \App\Models\User::count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Your Session</h5>
                <p class="text-success">Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Account Status</h5>
                <p class="text-success">Verified</p>
            </div>
        </div>
    </div>
</div>
@endsection