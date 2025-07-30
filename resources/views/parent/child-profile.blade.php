@extends('layouts.parent')

@section('title', 'Profile')

@section('content')
<div class="container">
    <h1 class="pb-3 pt-3 text-dark fw-bold">{{ $child->name }}'s Profile</h1>

    <div class="card mb-4 watercolor-card">
        <div class="card-body row align-items-center">
            <h5 class="card-title text-dark">Student Information</h5>
            <div class="col-md-3 text-center border-end border-secondary me-4">
                <div>
                    <img src="{{ asset('imgs/user.png') }}" alt="Profile Picture" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                </div>
                <h5 class="card-title text-dark mt-3">{{ $child->name }}</h5>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $child->id }}</p>
                        <p><strong>College:</strong> Information Technology</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Major:</strong> {{ $child->major ?? 'General Education' }}</p>
                        <p><strong>Year:</strong> {{ ucfirst($child->year) }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="">
                            <h6 class="text-dark fw-bold">CGPA</h6>
                            <div class="position-relative" style="width: 120px; height: 120px;">
                                <canvas id="gpaChart"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="fs-4 text-dark fw-bold">{{ number_format($gpa, 2) }}</span>/4.0
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="">
                            <h6 class="text-dark fw-bold">Credits</h6>
                            <div class="position-relative" style="width: 120px; height: 120px;">
                                <canvas id="creditsChart"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="fs-4 text-dark fw-bold">{{ $totalCredits }}</span>/{{ $maxCredits }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Personal Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Gender:</strong> 
                        @if($child->gender == 'male')
                            {{ ucfirst($child->gender) }} <i class="bi bi-gender-male text-primary"></i> 
                        @else
                            {{ ucfirst($child->gender) }} <i class="bi bi-gender-female text-danger"></i>
                        @endif
                    </p>
                    <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($child->birth_date)->age }} years</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> <a href="mailto:{{ $child->email }}" class="text-decoration-none text-dark">{{ $child->email }}</a></p>
                    <p><strong>Contact Number:</strong> <a href="tel:{{ $child->phone }}" class="text-decoration-none text-dark">{{ $child->phone }}</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Parent Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Parent Name:</strong> {{ $child->parent->name ?? 'N/A' }}</p>
                    <p><strong>Gender:</strong> 
                        @if($child->parent && $child->parent->gender == 'male')
                            {{ ucfirst($child->parent->gender) }} <i class="bi bi-gender-male text-primary"></i> 
                        @elseif($child->parent && $child->parent->gender == 'female')
                            {{ ucfirst($child->parent->gender) }} <i class="bi bi-gender-female text-danger"></i>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Parent Email:</strong> <a href="mailto:{{ $child->parent->email ?? '' }}" class="text-decoration-none text-dark">{{ $child->parent->email ?? 'N/A' }}</a></p>
                    <p><strong>Parent Contact:</strong> <a href="tel:{{ $child->parent->phone ?? '' }}" class="text-decoration-none text-dark">{{ $child->parent->phone ?? 'N/A' }}</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Feedbacks</h5>
            <div class="mb-3">
                @foreach ($feedbacks as $feedback)
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ asset('imgs/prof.png') }}" class="rounded-circle me-3" alt="User" width="40" height="40">
                        <div>
                            <strong class="text-primary">{{ $feedback->sender->name }}</strong>
                            <p class="mb-1">{{ $feedback->content }}</p>
                            <small class="text-muted">Rating: <span class="text-success">Excellent</span> · {{ Carbon\Carbon::parse($feedback->date)->format('Y M d') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // GPA Chart
        const gpaCtx = document.getElementById('gpaChart').getContext('2d');
        const gpa = {{ $gpa }};
        const maxGpa = 4.0;
        const gpaPercentage = (gpa / maxGpa) * 100;

        let gpaProgressColor;
        if (gpa >= 3.0) {
            gpaProgressColor = '#28a745'; 
        } else if (gpa <= 2.0) {
            gpaProgressColor = '#dc3545'; 
        } else {
            gpaProgressColor = '#007bff'; 
        }

        new Chart(gpaCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [gpaPercentage, 100 - gpaPercentage],
                    backgroundColor: [gpaProgressColor, '#e9ecef'],
                    borderWidth: 0,
                    circumference: 360,
                    rotation: -90,
                    cutout: '80%',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Credits Chart
        const creditsCtx = document.getElementById('creditsChart').getContext('2d');
        const totalCredits = {{ $totalCredits }};
        const maxCredits = {{ $maxCredits }};
        const creditsPercentage = (totalCredits / maxCredits) * 100;

        let creditsProgressColor;
        if (creditsPercentage >= 75) {
            creditsProgressColor = '#28a745'; // Green for >= 75%
        } else if (creditsPercentage <= 25) {
            creditsProgressColor = '#dc3545'; // Red for <= 25%
        } else {
            creditsProgressColor = '#007bff'; // Blue for 25% to 75%
        }

        new Chart(creditsCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [creditsPercentage, 100 - creditsPercentage],
                    backgroundColor: [creditsProgressColor, '#e9ecef'],
                    borderWidth: 0,
                    circumference: 360,
                    rotation: -90,
                    cutout: '80%',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    });
</script>

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
        color: #2c3e50;
    }
    p {
        margin-bottom: 0.75rem;
        color: #34495e;
    }
    .profile-image-container {
        background: #fff;
        padding: 10px;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .bi-gender-male {
        color: #007bff;
    }
    .bi-gender-female {
        color: #e83e8c;
    }
    a {
        transition: color 0.3s ease;
    }
    a:hover {
        color: #0056b3;
    }
</style>
@endsection