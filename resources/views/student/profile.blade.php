@extends('layouts.student')

@section('title', 'Profile')

@section('content')
    <h1 class="pb-5 pt-3 text-dark">Your Profile</h1>

    <!-- First Card: Student Data with GPA and Credits -->
    <div class="card mb-4 watercolor-card">
        <div class="card-body row">
            <div class="col-md-6">
                <h5 class="card-title text-dark">Student Information</h5>
                <p><strong>Full Name:</strong> {{ $user->name }}</p>
                <p><strong>Major:</strong> {{ $user->major }}</p>
                <p><strong>Year:</strong> {{ ucfirst($user->year) }}</p>
            </div>
            <div class="col-md-6 d-flex justify-content-around align-items-center">
                <div class="text-center">
                    <h6 class="text-dark fw-bold">CGPA</h6>
                    <div class="position-relative" style="width: 100px; height: 100px;">
                        <canvas id="gpaChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="fs-4 text-dark fw-bold">{{ $gpa }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <h6 class="text-dark fw-bold">Credits</h6>
                    <div class="position-relative" style="width: 100px; height: 100px;">
                        <canvas id="creditsChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="fs-4 text-dark fw-bold">{{ $totalCredits }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Card: Personal Information -->
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

    <!-- Third Card: Parent Information -->
    <div class="card watercolor-card">
        <div class="card-body">
            <h5 class="card-title text-dark">Parent Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Parent Name:</strong> {{ $user->parent->name ?? 'N/A' }}</p>
                    <p><strong>Gender:</strong> 
                        @if($user->parent->gender == 'male')
                            {{ ucfirst($user->parent->gender) }} <i class="bi bi-gender-male"></i> 
                        @else
                            {{ ucfirst($user->parent->gender) }} <i class="bi bi-gender-female"></i>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Parent Email:</strong> {{ $user->parent->email ?? 'N/A' }}</p>
                    <p><strong>Parent Contact:</strong> {{ $user->parent->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // GPA Chart
            const gpaCtx = document.getElementById('gpaChart').getContext('2d');
            const gpa = {{ $gpa }};
            const maxGpa = 4.0;
            const gpaPercentage = (gpa / maxGpa) * 100;

            let gpaProgressColor;
            if (gpa >= 3.0) {
                gpaProgressColor = '#28a745'; // Green for GPA >= 3.0
            } else if (gpa <= 2.0) {
                gpaProgressColor = '#dc3545'; // Red for GPA <= 2.0
            } else {
                gpaProgressColor = '#007bff'; // Blue for GPA between 2.0 and 3.0
            }

            new Chart(gpaCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [gpaPercentage, 100 - gpaPercentage],
                        backgroundColor: [gpaProgressColor, '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                        backgroundColor: ['#007bff', '#e9ecef'],
                        borderWidth: 0,
                        circumference: 360,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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