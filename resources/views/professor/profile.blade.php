@extends('layouts.professor')

@section('title', 'Profile')

@section('content')
    <h1 class="pb-5">Your Profile</h1>
    <div class="card" style="background-color: #495057; border: 1px solid #6c757d;">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone }}</p>
            <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($user->birth_date)->age }}</p>
            <p><strong>Role:</strong> Professor</p>
            <p><strong>Department:</strong> {{ $user->department }} </p>
        </div>
    </div>
@endsection