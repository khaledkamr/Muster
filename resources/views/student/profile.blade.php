@extends('layouts.student')

@section('title', 'Profile')

@section('content')
    <h1 class="pb-5 pt-3">Your Profile</h1>
    <div class="card" style="background-color: #495057; border: 1px solid #6c757d;">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($user->birth_date)->age }} years</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone }}</p>
            <p><strong>Major:</strong> {{ $user->major }}</p>
            <p><strong>Year:</strong> {{ ucfirst($user->year) }}</p>
        </div>
    </div>
@endsection