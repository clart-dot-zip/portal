@extends('layouts.app')

@section('title', 'Welcome to Portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-pink-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 text-center">
            <div class="mb-8">
                <img src="{{ asset('images/clart.png') }}" alt="Portal Logo" class="w-20 h-20 mx-auto mb-4 rounded-full shadow-lg">
                <h1 class="text-3xl font-bold text-white mb-2">Welcome to Portal</h1>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-100 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('login') }}" 
                   class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    Sign In with Authentik
                </a>
                
                <p class="text-white/60 text-sm">
                    Sign in to access your dashboard and manage services
                </p>
            </div>
        </div>
    </div>
</div>
@endsection