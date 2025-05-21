@extends('layouts.student')

@section('title', 'Course Details - ' . $course->name)

@section('content')
    <style>
        .grades .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.288);
        }
    </style>
    <div class="d-flex align-items-center mb-2 pt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-dark fw-bold mb-0">{{ $course->code }}</h1>
        
    </div>

    <div class="d-flex align-items-center mb-3 ">
        <h4 class="text-dark fw-bold mb-0">{{ $course->name }}</h4>
        <span class="text-dark ms-3">{{ $course->credit_hours }} HRs</span>
    </div>

    <div class="text-center mb-4">
        <div class="position-relative d-inline-block" style="width: 200px; height: 150px;">
            <canvas id="totalScoreChart"></canvas>
            <div class="position-absolute top-50 start-50 translate-middle text-center">
                <span class="fs-4 text-dark fw-bold d-block">{{ $percentage }}%</span>
                <span class="fs-6 text-dark">{{ $totalScore }}/{{ $totalMaxScore }}</span>
            </div>
        </div>
        <h5 class="text-dark mt-2">Result</h5>
    </div>

    <div class="container">
        <div class="grades row justify-content-left mb-4">
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Quiz 1</h6>
                        <span class="text-dark">{{ $displayScores['quiz1'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['quiz1'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Midterm</h6>
                        <span class="text-dark">{{ $displayScores['midterm'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['midterm'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Quiz 2</h6>
                        <span class="text-dark">{{ $displayScores['quiz2'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['quiz2'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Project</h6>
                        <span class="text-dark">{{ $displayScores['project'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['project'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Assignments</h6>
                        <span class="text-dark">{{ $displayScores['assignments'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['assignments'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-dark fw-bold">Final</h6>
                        <span class="text-dark">{{ $displayScores['final'] }}</span>
                        <span class="text-success">/{{ $displayMaxScores['final'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Final Grade and Status -->
    <div class="text-center text-dark">
        @if(!empty($grade->grade))
            <h4>Final Grade: <span class="badge bg-{{ $grade->status === 'pass' ? 'success' : 'danger' }}">{{ $grade->grade }}</span></h4>
            <p>Status: <strong>{{ ucfirst($grade->status) }}</strong></p>
        @endif
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Total Score Chart
            const totalScoreCtx = document.getElementById('totalScoreChart').getContext('2d');
            const totalScore = {{ $totalScore }};
            const maxScore = {{ $totalMaxScore }};
            const scorePercentage = (totalScore / maxScore) * 100;

            let progressColor;
            if (scorePercentage >= 75) {
                progressColor = '#28a745'; // Green for >= 75%
            } else if (scorePercentage <= 50) {
                progressColor = '#dc3545'; // Red for <= 50%
            } else {
                progressColor = '#007bff'; // Blue for 50% to 75%
            }

            new Chart(totalScoreCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [scorePercentage, 100 - scorePercentage],
                        backgroundColor: [progressColor, '#e9ecef'],
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
        .card {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 1rem;
        }
    </style>
@endsection