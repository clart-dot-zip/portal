@extends('layouts.guest')

@section('title', 'Welcome to Portal')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('images/clart.png') }}" alt="Portal Logo" class="img-circle elevation-3 mb-3" style="width: 72px; height: 72px;">
        <h1 class="h4 text-dark mb-1">Welcome to {{ config('app.name', 'Portal') }}</h1>
        <p class="text-muted mb-0">Secure access to your Authentik-powered services</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <a href="{{ route('login') }}" class="btn btn-portal-primary btn-block mb-3">
        <i class="fas fa-lock mr-2"></i> Sign In with Authentik
    </a>

    <p class="text-center text-muted small mb-0">
        Use your organization account to continue.
    </p>
@endsection