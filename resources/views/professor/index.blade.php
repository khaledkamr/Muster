@extends('layouts.professor')

@section('title', 'Home')

@section('content')
    <div class="jumbotron text-center mt-3 bg-dark text-white rounded-3">
        <h1 class="display-4">Welcome, {{ $user->name }}!</h1>
        <p class="lead">Use the sidebar to navigate your dashboard.</p>
    </div>
@endsection