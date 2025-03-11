@extends('layouts.student')

@section('title', 'Home')

@section('content')
    <div class="jumbotron text-center mt-3" style="background-color: #495057; border-radius: 10px;">
        <h1 class="display-4">Welcome, {{ $user->name }}!</h1>
        <p class="lead">Use the sidebar to navigate your dashboard.</p>
    </div>
@endsection