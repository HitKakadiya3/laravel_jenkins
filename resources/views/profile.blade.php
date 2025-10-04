<!-- filepath: c:\wamp64\www\laravel_jenkins\resources\views\profile.blade.php -->
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>User Profile</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" value="{{ auth()->user()->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Member Since</label>
                                <input type="text" class="form-control" id="created_at" value="{{ auth()->user()->created_at->format('M d, Y') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="updated_at" class="form-label">Last Updated</label>
                                <input type="text" class="form-control" id="updated_at" value="{{ auth()->user()->updated_at->format('M d, Y H:i') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_id" class="form-label">User ID</label>
                        <input type="text" class="form-control" id="user_id" value="#{{ auth()->user()->id }}" readonly>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-edit"></i> Edit Profile (Coming Soon)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Account Actions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Manage your account settings and preferences.</p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <button class="btn btn-outline-warning btn-sm" disabled>
                        <i class="fas fa-key"></i> Change Password
                    </button>
                    <button class="btn btn-outline-info btn-sm" disabled>
                        <i class="fas fa-cog"></i> Account Settings
                    </button>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Account Statistics</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <div class="mb-2">
                        <strong>Account Created:</strong><br>
                        {{ auth()->user()->created_at->diffForHumans() }}
                    </div>
                    <div class="mb-2">
                        <strong>Profile Updated:</strong><br>
                        {{ auth()->user()->updated_at->diffForHumans() }}
                    </div>
                    <div>
                        <strong>Email Verified:</strong><br>
                        {{ auth()->user()->email_verified_at ? 'Yes' : 'No' }}
                    </div>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection