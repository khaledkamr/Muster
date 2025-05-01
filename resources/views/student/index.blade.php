@extends('layouts.student')

@section('title', 'Home')

@section('content')
    <h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{$user->name}}</h2>

    <div class="row mb-2">
        <div class="col-md-6">
            <div class="bg-body shadow rounded mb-3 d-flex position-relative pt-2 pb-2" style="height: 270px;">
                <!-- Left Part -->
                <div class="d-flex flex-column align-items-center justify-content-center" style="width: 30%; border-right: 1px solid #ddd;">
                    <img src="{{asset('imgs/user.png')}}" alt="Profile Picture" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 class="fw-bold text-dark">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->year }}</p>
                </div>
                <!-- Right Part -->
                <div class="d-flex flex-column justify-content-center px-3" style="width: 70%;">
                    <div class="">
                        <p class="mb-1 text-dark"><strong>Email:</strong> {{ $user->email }}</p>
                        <p class="mb-1 text-dark"><strong>Phone:</strong> {{ $user->phone }}</p>
                    </div>
                    <hr class="text-muted">
                    <div class="">
                        <h6 class="fw-bold text-dark">GPA Progress</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $user->gpa_progress }}%;" aria-valuenow="{{ $user->gpa_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted">{{ $user->gpa }} / 4.0</p>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark">Credit Hours Progress</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $user->credit_hours_progress }}%;" aria-valuenow="{{ $user->credit_hours_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted">{{ $user->completed_credit_hours }} / {{ $user->total_credit_hours }} hours</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bg-body shadow rounded mb-3 p-3 d-flex flex-column justify-content-between" style="height: 270px;">
            <h5 class="fw-bold text-dark">Upcoming Assignments</h5>
            {{-- @if ($upcomingAssignments->isNotEmpty()) --}}
                <ul class="list-group list-group-flush">
                {{-- @foreach ($upcomingAssignments as $assignment) --}}
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>assignment title</span>
                        <span class="badge bg-warning">2025-05-10</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>assignment title</span>
                        <span class="badge bg-warning">2025-05-10</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>assignment title</span>
                        <span class="badge bg-warning">2025-05-10</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>assignment title</span>
                        <span class="badge bg-warning">2025-05-10</span>
                    </li>
                {{-- @endforeach --}}
                </ul>
            {{-- @else
                <p class="text-muted">No upcoming assignments.</p>
            @endif --}}
            <a href="{{ route('student.assignments') }}" class="btn btn-primary mt-2 align-self-end">View All Assignments</a>
            </div>
        </div>
    </div>

    <h3 class="text-dark fw-bold">Your courses</h3>
    @if ($currentSemesterCourses->isNotEmpty())
        <!-- Swiper Slider -->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach ($currentSemesterCourses as $course)
                    <div class="swiper-slide">
                        <div class="course-card card shadow-sm" style="background: linear-gradient(135deg, #20ff32cb, #000000); color: white; position: relative;">
                            <span class="badge position-absolute top-0 end-0 m-2" style="font-size: 0.9rem;">
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
                                        <strong>By</strong> {{ $course->professor->name }}<br>
                                    </p>
                                    <a href="{{ route('student.course-details', $course->id) }}" class="btn btn-light">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Navigation Buttons -->
            {{-- <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div> --}}
            <!-- Pagination -->
            {{-- <div class="swiper-pagination"></div> --}}
        </div>
    @endif

    <!-- Include Swiper.js -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 20,
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 3,
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
                loop: false,
                autoplay: {
                    delay: 10000,
                    disableOnInteraction: false,
                },
            });
        });
    </script>

    <style>
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
        .swiper-container {
            position: relative;
            padding-bottom: 40px;
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
    </style>
@endsection