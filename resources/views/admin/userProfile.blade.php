@extends('layouts.admin')

@section('title', 'user profile')

@section('content')
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
<div class="container">
    <div class="d-flex align-items-center mb-4 pt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-dark fw-bold mb-0">User's Profile</h1>
    </div>
    @if($user->role == 'student')
        <div class="card mb-4 watercolor-card">
            <div class="card-body row align-items-center">
                <h5 class="card-title text-dark">Student Information</h5>
                <div class="col-md-3 text-center border-end border-secondary me-4">
                    <div>
                        <img src="{{ asset('imgs/user.png') }}" alt="Profile Picture" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                    </div>
                    <h5 class="card-title text-dark mt-3">{{ $user->name }}</h5>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> {{ $user->id }}</p>
                            <p><strong>College:</strong> Information Technology</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Major:</strong> {{ $user->major ?? 'General Education' }}</p>
                            <p><strong>Year:</strong> {{ ucfirst($user->year) }}</p>
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
                            @if($user->gender == 'male')
                                {{ ucfirst($user->gender) }} <i class="bi bi-gender-male text-primary"></i> 
                            @else
                                {{ ucfirst($user->gender) }} <i class="bi bi-gender-female text-danger"></i>
                            @endif
                        </p>
                        <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($user->birth_date)->age }} years</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark">{{ $user->email }}</a></p>
                        <p><strong>Contact Number:</strong> <a href="tel:{{ $user->phone }}" class="text-decoration-none text-dark">{{ $user->phone }}</a></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 watercolor-card">
            <div class="card-body">
                <h5 class="card-title text-dark">Parent Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Parent Name:</strong> {{ $student->parent->name ?? 'N/A' }}</p>
                        <p><strong>Gender:</strong> 
                            @if($user->parent && $user->parent->gender == 'male')
                                {{ ucfirst($user->parent->gender) }} <i class="bi bi-gender-male text-primary"></i> 
                            @elseif($user->parent && $user->parent->gender == 'female')
                                {{ ucfirst($user->parent->gender) }} <i class="bi bi-gender-female text-danger"></i>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Parent Email:</strong> <a href="mailto:{{ $user->parent->email ?? '' }}" class="text-decoration-none text-dark">{{ $user->parent->email ?? 'N/A' }}</a></p>
                        <p><strong>Parent Contact:</strong> <a href="tel:{{ $user->parent->phone ?? '' }}" class="text-decoration-none text-dark">{{ $user->parent->phone ?? 'N/A' }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($user->role == 'professor')
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
                    <p><strong>Department:</strong> {{ $user->department }}</p>
                </div>
            </div>
        </div>

        <div class="card mb-4 watercolor-card">
            <div class="card-body">
                <h5 class="card-title text-dark">Professor's courses</h5>
                @if($user->courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="background-color: transparent;" >Course Code</th>
                                    <th class="text-center" style="background-color: transparent;">Course Name</th>
                                    <th class="text-center" style="background-color: transparent;">Department</th>
                                    <th class="text-center" style="background-color: transparent;">Credits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->courses as $course)
                                    <tr>
                                        <td class="text-center" style="background-color: transparent;">{{ $course->code }}</td>
                                        <td class="text-center" style="background-color: transparent;">{{ $course->name }}</td>
                                        <td class="text-center" style="background-color: transparent;">{{ $course->department }}</td>
                                        <td class="text-center" style="background-color: transparent;">{{ $course->credit_hours }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No courses assigned yet.</p>
                @endif
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
    @elseif($user->role == 'parent')
        <div class="card mb-4 watercolor-card">
            <div class="card-body row align-items-center">
                <h5 class="card-title text-dark">User Information</h5>
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
    @elseif($user->role == 'admin')
        <div class="card mb-4 watercolor-card">
            <div class="card-body row align-items-center">
                <h5 class="card-title text-dark">User Information</h5>
                <div class="col-md-3 text-center border-end border-secondary me-4">
                    <div>
                        <img src="{{ asset('imgs/admin.png') }}" alt="Profile Picture" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                    </div>
                    <h5 class="card-title text-dark mt-3">{{ $user->name }}</h5>
                </div>
                <div class="col-md-8">
                    <p><strong>Full Name:</strong> {{ $user->name }}</p>
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>ID:</strong> {{ $user->role }}</p>
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
    @endif

    <div class="card mb-4 watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Feedbacks</h5>
            <div class="mb-3">
                @foreach ($feedbacks as $feedback)
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ asset('imgs/user.png') }}" class="rounded-circle me-3" alt="User" width="40" height="40">
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
@endsection