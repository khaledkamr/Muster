@extends('layouts.student')

@section('title', 'Home')

@section('content')
    <h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{$user->name}}</h2>

    <div class="row mb-2">
        <!-- profile card -->
        <div class="col-md-6">
            <div class="bg-body shadow rounded mb-3 d-flex position-relative pt-2 pb-2" style="height: 270px;">
                <!-- Left Part -->
                <div class="d-flex flex-column align-items-center justify-content-center" style="width: 30%; border-right: 1px solid #ddd;">
                    <a href="{{ route('student.profile') }}">
                        <img src="{{asset('imgs/user.png')}}" alt="Profile Picture" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                    </a>
                    <h5 class="fw-bold text-muted">{{ $user->name }}</h5>
                    <p class="text-muted mb-1">{{ $user->year }}</p>
                    <p class="text-muted">{{ $major }}</p>
                </div>
                <!-- Right Part -->
                <div class="d-flex flex-column justify-content-center px-3" style="width: 70%;">
                    <div class="pt-2">
                        <h6 class="fw-bold text-dark">Attendance Rate</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceRate }}%;" aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-2">{{ $attendanceRate }} %</p>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark">GPA Progress</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $gpa_progress }}%;" aria-valuenow="{{ $gpa_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-2">{{ $gpa }} / 4.0</p>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark">Credit Hours Progress</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" srole="progressbar" style="width: {{ $credits_progress }}%;" aria-valuenow="{{ $credits_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-2">{{ $totalCredits }} / {{ $maxCredits }} hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- upcoming assignments -->
        <div class="col-md-6">
            <div class="bg-body shadow rounded mb-3 p-3 d-flex flex-column justify-content-between" style="height: 270px;">
                <h5 class="fw-bold text-dark">Upcoming Assignments</h5>
                @if (!empty($upcomingAssignments))
                    <ul class="list-group list-group-flush">
                        @for ($i = 0; $i < 4; $i++)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{$upcomingAssignments[0]}}</span>
                                <span class="text-muted">{{ $currentSemesterCourses[$i]->code }}</span>
                                <span class="badge bg-info">2025-11-10</span>
                            </li>
                        @endfor
                    </ul>
                @else
                    <p class="text-muted">No upcoming assignments.</p>
                @endif
                <a href="{{ route('student.assignments') }}" class="btn btn-primary mt-2 align-self-end" style="background-color: var(--primary);">View All Assignments</a>
            </div>
        </div>
    </div>

    <!-- Your courses -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="bg-body shadow p-3 rounded courses-swiper">
                <h4 class="text-dark fw-bold">Your courses</h4>
                    @if ($currentSemesterCourses->isNotEmpty())
                        <!-- Swiper Slider -->
                        <div class="swiper-container position-relative pb-2">
                            <div class="swiper-wrapper">
                                @foreach ($currentSemesterCourses as $course)
                                    <div class="swiper-slide">
                                        <div class="course-card card shadow-sm" style="background: linear-gradient(135deg, var(--secondary), #000000); color: white; position: relative;">
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
            </div>
        </div>
    </div>

    <div class="row pb-4">
        <!-- Resent news -->
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

        <!-- upcoming events -->
        <div class="col-md-3">
            <div class="bg-body shadow p-3 rounded">
                <h5 class="text-dark fw-bold pb-3">Upcoming Events</h5>
                <div class="d-flex flex-column align-items-center overflow-hidden">
                    <div class="card shadow rounded mb-4" style="width: 100%; height: 200px;">
                        <div class="card-img-top" style="height: 50%; background-image: url('{{ asset('imgs/event-1.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="card-body d-flex flex-column justify-content-center" style="height: 50%;">
                            <h6 class="card-title text-dark fw-bold">MUST Career Summit</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-location-dot text-muted"></i>
                                <p class="card-text text-muted ms-2">MUST Golf Garden</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-calendar-days text-muted"></i>
                                <p class="card-text text-muted ms-2">Monday, April 14th</p>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow rounded mb-2" style="width: 100%; height: 200px;">
                        <div class="card-img-top" style="height: 50%; background-image: url('{{ asset('imgs/event-2.jpg') }}'); background-size: cover; background-position: center;"></div>
                        <div class="card-body d-flex flex-column justify-content-center" style="height: 50%;">
                            <h6 class="card-title text-dark fw-bold">Universities League</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-location-dot text-muted"></i>
                                <p class="card-text text-muted ms-2">MUST stadium</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-calendar-days text-muted"></i>
                                <p class="card-text text-muted ms-2">Monday, April 14th</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Offers -->
            <div class="bg-body shadow rounded p-3 mb-4" style="height: 240px;">
                <div class="d-flex"> 
                    <h5 class="text-dark fw-bold pb-3">Offers</h5>
                    <h6 class="text-dark ms-2 pt-1"> | Only for you</h6>
                </div>
                <div class="d-flex flex-column">
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-tag text-primary pe-2"></i> Get 20% off on your next purchase with code <strong>STUDENT20</strong>
                    </p>
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-gift text-primary pe-2"></i> Special discount on campus bookstore: 15% off on all textbooks
                    </p>
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-graduation-cap text-primary pe-2"></i> Early bird registration bonus: Save 10% on next semester courses
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-laptop text-primary pe-2"></i> Student laptop program: Exclusive prices on selected models
                    </p>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-primary mt-3" style="background-color: var(--primary);">View All Offers</a>
                </div>
            </div>

            <!-- Contribution Graph -->
            <div class="card shadow border-0" style="max-width: 500px;">
                <div class="card-body">
                    <h5 class="text-dark fw-bold mb-1">Contribution Graph</h5>
                    <div class="contribution-graph">
                        <!-- Month Labels -->
                        <div class="d-flex">
                            @php
                                $currentMonth = $semesterStart->copy();
                                $monthsInSemester = [];
                                while ($currentMonth <= $semesterEnd) {
                                    $monthLabel = $currentMonth->format('M');
                                    $monthDays = $currentMonth->copy()->startOfMonth()->diffInDays($currentMonth->copy()->endOfMonth()) + 1;
                                    $monthWidth = $monthDays * 3.2; 
                                    $monthsInSemester[$monthLabel] = $monthWidth;
                                    $currentMonth->addMonth();
                                }
                            @endphp
                            <div style="width: 65px;"></div>
                            @foreach ($monthsInSemester as $month => $width)
                                <div class="calendar-day-header" style="min-width: {{ $width }}px;">{{ $month }}</div>
                            @endforeach
                        </div>
                        <!-- Days of the Week -->
                        <div class="d-flex">
                            <div class="d-flex flex-column me-2" style="height: 110px; margin-top: -4px;">
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">sat</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">sun</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">mon</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">tue</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">wed</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">thu</div>
                                <div class="text-dark align-self-end mb-1" style="height: 14px;">fri</div>
                            </div>
                            <div class="d-flex flex-column flex-wrap" style="height: 130px;">
                                @php
                                    $currentDate = $semesterStart->copy();
                                    $firstDayOfSemester = $semesterStart->copy()->startOfWeek();
                                    $offset = $firstDayOfSemester->diffInDays($semesterStart, false);
                                    $x = 0;
                                @endphp
                                @while ($currentDate <= $semesterEnd)
                                    @php
                                        $dateStr = $currentDate->format('Y-m-d');
                                        $attended = isset($contributionData[$dateStr]) && $contributionData[$dateStr] == 1;
                                        $dayClass = $attended ? 'bg-success' : 'bg-secondary';
                                    @endphp
    
                                    @if($currentDate->copy()->format('D') != 'Sat' && $x == 0)
                                        @php
                                            $dayOfWeek = $currentDate->copy()->dayOfWeek; // 0 (Sun) to 6 (Sat)
                                        @endphp
                                        @for ($i = 0; $i < $dayOfWeek + 1; $i++)
                                            <div class="contribution-day bg-body" style="width: 14px; height: 14px; margin: 2px;"></div>
                                        @endfor
                                    @endif
                                    @php
                                        $x++;
                                    @endphp
    
                                    <div class="contribution-day {{ $dayClass }}" style="width: 14px; height: 14px; margin: 2px;" title="{{ $attended ? 'attended on' : 'absent on' }} {{ $currentDate->format('M d, D') }}"></div>
                                    
                                    @if($currentDate->copy()->endOfMonth()->format('d-m-y') == $currentDate->format('d-m-y'))
                                        @for ($i = 0; $i < 7; $i++)
                                            <div class="contribution-day bg-body" style="width: 14px; height: 14px; margin: 2px;"></div>
                                        @endfor
                                    @endif
                                    @php
                                        $currentDate->addDay();
                                    @endphp
                                @endwhile
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="d-flex align-items-center mt-2 justify-content-end">
                            <div class="contribution-day bg-secondary me-1" style="width: 14px; height: 14px;"></div>
                            <span class="text-dark me-2">absent</span>
                            <div class="contribution-day bg-success" style="width: 14px; height: 14px;"></div>
                            <span class="text-dark ms-1">attended</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly attendance -->
    <div class="row">
        <div class="col-md-12">
            <div class="bg-body shadow rounded p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="text-dark fw-bold">Weekly Attendance</h5>
                    <a href="{{ route('student.attendance') }}" class="btn btn-primary rounded"  style="background-color: var(--primary);">View All Attendance</a>
                </div>
                <div>
                    <canvas id="weeklyAttendanceChart" height="70"></canvas>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Include Swiper.js -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            // Weekly Attendance Graph
            const weeklyAttendanceCtx = document.getElementById('weeklyAttendanceChart').getContext('2d');
            const weeklyData = @json($weeklyAttendance);
            const labels = Object.keys(weeklyData).map(week => `Week ${week}`);
            const data = Object.values(weeklyData);

            new Chart(weeklyAttendanceCtx, {
                type: 'line', 
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Attended Sessions',
                        data: data,
                        borderColor: '#002361',
                        backgroundColor: '#00246171',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: false,
                                text: 'Week Number'
                            },
                            max: 17,
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Sessions'
                            },
                            beginAtZero: true,
                            max: {{ $currentSemesterCourses->count() * 2 }},
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
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
        .contribution-day {
            border-radius: 3px;
            cursor: pointer;
            transition: 0.3s;
        }
        .contribution-day:hover {
            opacity: 0.8;
        }
    </style>
@endsection