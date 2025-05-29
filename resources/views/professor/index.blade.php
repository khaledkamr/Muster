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
                            <div class="stat-item mb-1 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-book-open me-2"></i>Courses Taught</p>
                                <span class="badge bg-blue">{{ $professor->courses->count() }}</span>
                            </div>
                            <div class="stat-item mb-1 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-comments me-2"></i>Discussions</p>
                                <span class="badge bg-blue">25</span>
                            </div>
                            <div class="stat-item mb-0 d-flex justify-content-between align-items-center">
                                <p class="text-muted mb-0"><i class="fas fa-comment-dots me-2"></i>Feedback</p>
                                <span class="badge bg-blue">423</span>
                            </div>
                        </div>
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
                    <div class="bg-body shadow p-3 rounded">
                        <h4 class="text-dark fw-bold pb-1">Courses Overview</h4>
                        @if ($courses->isNotEmpty())
                            <div class="row">
                                @foreach ($courses->take(2) as $course)
                                    <div class="col-md-6 mb-1">
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
                                                    <div class="btn btn-light">{{ $course->enrollments->where('enrolled_at', '>=', '2025-08-01')->count() }} students</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Resent News -->
        <div class="col-md-4">
            <div class="bg-body shadow rounded p-3">
                <h5 class="text-dark fw-bold pb-1">Resent News</h5>
                <div class="d-flex flex-column">
                    <div class="d-flex justify-content-between border-bottom pb-1 mb-2">
                        <a href="https://must.edu.eg/participation-of-the-college-of-oral-and-dental-surgery-in-the-symposium-the-role-of-egyptian-women-through-the-ages-in-building-egyptian-society/">
                            <img src="{{ asset('imgs/news-1.jpg') }}" alt="News Image" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        </a>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Participation of the College of Oral and Dental Surgery in the Symposium “The Role of Egyptian Women Through the Ages in Building Egyptian Society.
                            </p>
                            <small class="badge text-muted text-end d-block"><i class="fa-solid fa-calendar-days"></i> Apr 9, 2025</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-1 mb-2">
                        <a href="https://must.edu.eg/participation-of-the-college-of-oral-and-dental-surgery-in-the-reading-club-event-titled-a-tribute-worthy-of-immortality-on-the-occasion-of-mothers-day/">
                            <img src="{{ asset('imgs/news-2.jpg') }}" alt="News Image" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        </a>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Participation of the College of Oral and Dental Surgery in the Reading Club Event Titled “A Tribute Worthy of Immortality” on the Occasion of Mother’s Day.
                            </p>
                            <small class="badge text-muted text-end d-block"><i class="fa-solid fa-calendar-days"></i> Apr 9, 2025</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-1 mb-2">
                        <a href="https://must.edu.eg/participation-of-the-college-of-dentistry-and-oral-surgery-at-misr-university-for-science-and-technology-in-mednotch-medicals-iftar-event/">
                            <img src="{{ asset('imgs/news-3.jpg') }}" alt="News Image" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        </a>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Participation of the College of Dentistry and Oral Surgery at Misr University for Science and Technology in Mednotch Medical’s Iftar Event.
                            </p>
                            <small class="badge text-muted text-end d-block"><i class="fa-solid fa-calendar-days"></i> Apr 9, 2025</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="https://must.edu.eg/misr-university-for-science-and-technology-honors-winners-of-2025-annual-quran-competition/">
                            <img src="{{ asset('imgs/news-4.jpg') }}" alt="News Image" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        </a>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Misr University for Science and Technology Honors Winners of 2025 Annual Quran Competition.
                            </p>
                            <small class="badge text-muted text-end d-block"><i class="fa-solid fa-calendar-days"></i> Mar 26, 2025</small>
                        </div>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .bg-blue {
        background-color: #002361; 
    }
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
    .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection