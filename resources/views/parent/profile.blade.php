@extends('layouts.parent')

@section('title', 'Profile')

@section('content')
<div class="container">
    <h1 class="pb-5 pt-3 text-dark fw-bold">Your Profile</h1>

    <div class="card mb-4 watercolor-card">
        <div class="card-body row align-items-center">
            <h5 class="card-title text-dark">professor Information</h5>
            <div class="col-md-3 text-center border-end border-secondary me-4">
                <div>
                    <img src="{{ asset('imgs/prof.png') }}" alt="Profile Picture" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                </div>
                <h5 class="card-title text-dark mt-3">{{ $user->name }}</h5>
            </div>
            <div class="col-md-8">
                <p><strong>Full Name:</strong> {{ $user->name }}</p>
                <p><strong>ID:</strong> {{ $user->id }}</p>
            </div>
        </div>
    </div>

    <div class="card mb-4 watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Personal Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Gender:</strong> 
                        @if($user->gender == 'male')
                            {{ ucfirst($user->gender) }} <i class="bi bi-gender-male"></i> 
                        @else
                            {{ ucfirst($user->gender) }} <i class="bi bi-gender-female"></i>
                        @endif
                    </p>
                    <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($user->birth_date)->age }} years</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Contact Number:</strong> {{ $user->phone }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <style>
        .watercolor-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(170, 253, 177, 0.7));
            border: 2px solid rgba(147, 112, 219, 0.5);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .watercolor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/canvas.png');
            opacity: 0.1;
            z-index: 0;
        }

        .watercolor-card .card-body {
            position: relative;
            z-index: 1;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 0.5rem;
            color: #333;
        }

        p strong {
            color: #555;
        }
    </style>
@endsection