@extends('layouts.professor')

@section('title', 'Exams')

@section('content')
    <div class="container">
        <h2 class="text-dark fw-bold pt-3 pb-4">{{ $course->name }} / Exams</h2>

        <div class="accordion" id="examsAccordion">
            @foreach ($examStats as $examType => $stats)
                @if (!empty($stats['grades']))
                    <div class="accordion-item border-0 rounded-4 shadow-sm mb-3">
                        <h2 class="accordion-header" id="heading{{ $examType }}">
                            <button class="accordion-button collapsed bg-white shadow-sm rounded-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $examType }}" aria-expanded="false"
                                aria-controls="collapse{{ $examType }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div>
                                        <h4 class="mb-1 text-dark">{{ $stats['name'] }}</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">Avg: {{ $stats['average_grade'] }}</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $examType }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $examType }}" data-bs-parent="#examsAccordion">
                            <div class="accordion-body bg-white shadow-sm rounded-3 mt-2">
                                <!-- Statistics Cards -->
                                <div class="row mb-5">
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                                            Highest Grade
                                                        </h5>
                                                        <p class="card-text text-muted d-flex align-items-center">
                                                            <img src="{{ asset('imgs/user.png') }}" alt="Student"
                                                                class="me-2" style="width: 24px; height: 24px;">
                                                            {{ $stats['max_grade']['student_name'] }}
                                                            <i class="fas fa-crown text-muted ms-2"></i>
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <h3 class="card-title fs-1 text-success fw-bold">
                                                            {{ $stats['max_grade']['value'] }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                                            Lowest Grade
                                                        </h5>
                                                        <p class="card-text text-muted d-flex align-items-center">
                                                            <img src="{{ asset('imgs/user.png') }}" alt="Student"
                                                                class="me-2" style="width: 24px; height: 24px;">
                                                            {{ $stats['min_grade']['student_name'] }}
                                                            <i class="fas fa-thumbs-down text-muted ms-2"></i>
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <h3 class="card-title fs-1 text-danger fw-bold">
                                                            {{ $stats['min_grade']['value'] }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="card-subtitle mb-2 text-dark fw-bold">
                                                            Average Grade
                                                        </h5>
                                                        <p class="card-text text-muted d-flex align-items-center">
                                                            <i class="fas fa-users me-2"></i> Total Students:
                                                            {{ $stats['grades']->total() }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <h3 class="card-title fs-1 text-primary fw-bold">
                                                            {{ $stats['average_grade'] }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Students Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Student ID</th>
                                                <th class="text-center">Student Name</th>
                                                <th class="text-center">Grade <i class="fa-solid fa-arrow-down-9-1"></i>
                                                </th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stats['grades'] as $grade)
                                                <tr>
                                                    <td class="text-center">{{ $grade['student_id'] }}</td>
                                                    <td class="text-center">{{ $grade['student_name'] }}</td>
                                                    <td class="text-center">{{ $grade['grade'] }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('professor.student.profile', ['studentId' => $grade['student_id'], 'courseId' => $courseId]) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $stats['grades']->onEachSide(1)->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="accordion-item border-0 rounded-4 shadow-sm mb-3">
                        <h2 class="accordion-header" id="heading{{ $examType }}">
                            <button class="accordion-button collapsed bg-white shadow-sm rounded-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $examType }}"
                                aria-expanded="false" aria-controls="collapse{{ $examType }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div>
                                        <h4 class="mb-1 text-dark">{{ $stats['name'] }}</h4>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">Avg: {{ $stats['average_grade'] }}</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $examType }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $examType }}">
                            <div class="accordion-body">
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This exam has not been conducted yet. No grades are available to display.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Exam Averages Chart -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h4 class="text-dark fw-bold mb-4">Exam Averages Comparison</h4>
                <div class="chart-container" style="position: relative; height:400px;">
                    <canvas id="examAveragesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <style>
        .accordion-button:not(.collapsed) {
            background-color: #fff;
            color: #000;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, .125);
        }

        .accordion-button::after {
            margin-left: 1rem;
        }

        /* Pagination Styles */
        .pagination {
            margin-bottom: 0;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .page-link {
            color: #0d6efd;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
        }

        .page-link:hover {
            color: #0a58ca;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
    </style>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('examAveragesChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: 'Average Grade',
                            data: @json($chartData['averages']),
                            backgroundColor: @json($chartData['colors']),
                            borderColor: @json($chartData['colors']),
                            borderWidth: 1,
                            borderRadius: 5,
                            barThickness: 100,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Average: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 20,
                                title: {
                                    display: true,
                                    text: 'Average Grade'
                                },
                                ticks: {
                                    stepSize: 2
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Exam Type'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
@endsection
