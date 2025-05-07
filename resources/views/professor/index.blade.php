@extends('layouts.professor')

@section('title', 'Home')

@section('content')
<div class="container">
    <h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{$professor->name}}</h2>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="row mb-4">
                <!-- professor profile Box -->
                <div class="col-md-6">
                    <div class="bg-body shadow rounded d-flex position-relative p-3 d-flex flex-column">
                        <div class="d-flex align-items-center border-bottom pb-0 pb-3">
                            <a href="{{ route('professor.profile') }}">
                                <img src="{{asset('imgs/prof.png')}}" alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            </a>
                            <div class="ms-3">
                                <h5 class="text-dark fw-bold mb-1">{{ $professor->name }}</h5>
                                <p class="text-muted mb-1">{{ $professor->department }}</p>
                            </div>
                        </div>
                        <div class="info mt-3">
                            <div class="stat-item mb-2 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-book-open me-2"></i>Courses Taught</p>
                                <span class="badge">{{ $professor->courses->count() }}</span>
                            </div>
                            <div class="stat-item mb-2 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-comments me-2"></i>Discussions</p>
                                <span class="badge">25</span>
                            </div>
                            <div class="stat-item mb-2 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-comment-dots me-2"></i>Feedback</p>
                                <span class="badge">423</span>
                            </div>
                            <div class="stat-item mb-2 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fa-solid fa-face-smile me-2"></i>Rating:</p>
                                <div class="stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= 4)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <style>
                        .badge {
                            background-color: #002361;
                            font-size: 0.85rem;
                            padding: 0.4em 0.8em;
                        }
                        .stars {
                            font-size: 1.1rem;
                        }
                        </style>
                    </div>
                </div>

                <!-- Upcoming Events Box -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-dark fw-bold mb-4">Upcoming Events</h5>
                            @if (!empty($upcomingEvents))
                                <div class="events-container">
                                    @foreach ($upcomingEvents as $event)
                                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $event['title'] }}</h6>
                                                </div>
                                                <p class="mb-0 text-muted fw-bold">
                                                    <i class="far fa-calendar me-2"></i>{{ $event['date'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No upcoming events.</p>
                            @endif
                            <a href="#" class="btn btn-primary w-100 mt-3" style="background-color: #002361;">
                                <i class="far fa-calendar-alt me-2"></i>View Calendar
                            </a>
                        </div>
                    </div>
                    <style>
                        .event-card {
                            transition: transform 0.2s ease;
                            border-left: 4px solid #002361;
                        }
                        .event-card:hover {
                            transform: translateX(5px);
                        }
                        .events-container {
                            max-height: 300px;
                            overflow-y: auto;
                        }
                    </style>
                </div>
            </div>
            <div class="row">
                
                <!-- Courses Overview Box -->
                <div class="col-md-12">
                    <div class="bg-body shadow p-3 rounded courses-swiper">
                        <h4 class="text-dark fw-bold pb-3">Courses Overview</h4>
                        @if ($courses->isNotEmpty())
                            <!-- Swiper Slider -->
                            <div class="swiper-container position-relative pb-2">
                                <div class="swiper-wrapper">
                                    @foreach ($courses as $course)
                                        <div class="swiper-slide">
                                            <div class="course-card card shadow-sm" style="background: linear-gradient(135deg, #0A9442, #000000); color: white;">
                                                <span class="badge position-absolute top-0 end-0" style="font-size: 0.9rem;">
                                                    {{ ucfirst($course->difficulty) }}
                                                </span>
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $course->code }} </h5>
                                                    <h3 class="fw-bold">{{ $course->name }}</h3>
                                                    <p class="card-text">
                                                        {{ $course->description }}
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <p class="align-self-start">
                                                            {{ $course->professor->name }}<br>
                                                        </p>
                                                        <div class="btn btn-light">{{ $course->enrollments->count() }} students</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Analytics -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-3">Course Analytics</h5>
                    <div class="row">
                        <div class="col-md-12" style="position: relative; height: 250px;">
                            <canvas id="attendancePieChart"></canvas>
                        </div>
                        <div class="col-md-12" style="position: relative; height: 250px;">
                            <canvas id="submissionPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const presentCount = {{ $allAttendanceRecords->where('status', 'present')->count() }};
    const absentCount = {{ $allAttendanceRecords->where('status', 'absent')->count() }};
    const lateCount = {{ $allAttendanceRecords->where('status', 'late')->count() }};
    // const total = presentCount + absentCount + lateCount;

    const pieCtx = document.getElementById('attendancePieChart').getContext('2d');
    const attendancePieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [presentCount, absentCount, lateCount],
                backgroundColor: ['#79f596', '#ff808a', '#ffcc00'],
                borderWidth: 1,
                borderColor: '#eee'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#333'
                    }
                },
                title: {
                    position: 'bottom',
                    display: true,
                    text: 'Attendance Status Distribution',
                    color: '#333',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            }
        }
    });

    const submittedCount = {{ $assignmentSubmissions->where('status', 'submitted')->count() }};
    const pendingCount = {{ $assignmentSubmissions->where('status', 'pending')->count() }};
    // const total = submittedCount + pendingCount;

    const ctx = document.getElementById('submissionPieChart').getContext('2d');
    const submissionPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Submitted', 'Pending'],
            datasets: [{
                data: [submittedCount, pendingCount],
                backgroundColor: ['#79f596', '#ff808a'],
                borderWidth: 1,
                borderColor: '#eee'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#333'
                    }
                },
                title: {
                    position: 'bottom',
                    display: true,
                    text: 'Assignments Submission Distribution',
                    color: '#333',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const swiper = new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                768: {
                    slidesPerView: 1,
                },
                992: {
                    slidesPerView: 2,
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            loop: true,
            autoplay: {
                delay: 10000,
                disableOnInteraction: false,
            },
        });
    });
</script>

<style>
    .courses-swiper {
        overflow: hidden;
    }
    .course-card {
        border: none;
        border-radius: 15px;
        /* height: 200px; */
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s;
    }
    .course-card:hover {
        transform: scale(1.02);
    }
    .course-card .badge {
        background-color: #00000025;
        color: white;
        padding: 0.5rem 1rem;
        border-bottom-left-radius: 20px;
        border-top-right-radius: 20px;
        font-size: 0.8rem;
    }
    .card-body {
        text-align: left;
    }
    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .card-text {
        font-size: 0.9rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Adjust this value to control the number of lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-light {
        border-radius: 20px;
        padding: 0.5rem 1.5rem;
        font-weight: bold;
    }
    .swiper-button-prev,
    .swiper-button-next {
        position: absolute;
        color: #007bff;
        opacity: 0.7;
        transition: opacity 0.3s;
        background-color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 10px;
    }
    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        position: absolute;
        background: #007bff;
    }
    .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection