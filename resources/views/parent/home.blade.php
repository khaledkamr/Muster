@extends('layouts.parent')

@section('title', 'Home')

@section('content')
<div class="container">
    <h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{ $user->name }}</h2>
    
    @foreach($children as $child)
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card bg-body shadow-sm border-0">
                    <div class="card-body d-flex">
                        <div class="col-md-3 text-center border-end me-4 d-flex flex-column justify-content-center align-items-center">
                            <div>
                                <img src="{{ asset('imgs/user.png') }}" alt="Profile Picture" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                            </div>
                            <h5 class="card-title text-dark mt-3">{{ $child->name }}</h5>
                            <h6 class="text-muted">{{ $child->year }}</h6>
                        </div>
                        <div class="col-md-3 border-end me-4 pe-4">
                            <h6 class="text-dark fw-bold mb-3">Enrolled Courses:</h6>
                            @if($child->enrollments->where('enrolled_at', '>=', '2025-08-01')->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($child->enrollments->where('enrolled_at', '>=', '2025-08-01') as $enrollment)
                                        <li class="list-group-item bg-transparent">
                                            <i class="fas fa-book me-2"></i>{{ $enrollment->course->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No courses enrolled yet.</p>
                            @endif
                        </div>
                        @php
                            $gradePoints = [ 'A+' => 4.0, 'A'  => 4.8, 'A-' => 3.7, 'B+' => 3.3, 'B'  => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C'  => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D'  => 1.0, 'D-' => 0.7, 'F'  => 0.0];

                            $totalPoints = 0;
                            $grades = $child->grades()->with('course')->get();
                            $totalCourses = $grades->count();

                            foreach ($grades as $grade) {
                                $totalPoints += $gradePoints[$grade->grade] ?? 0;
                            }

                            $cgpa = $totalCourses > 0 ? round($totalPoints / $totalCourses, 2) : 0.00;
                        @endphp
                        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center position-relative">
                            <h6 class="position-absolute text-dark fw-bold mb-3" style="top: 0; left: 0;">Academic Progress:</h6>
                            <div class="d-flex justify-content-center align-items-center gap-5 ms-0 ps-0 pe-5">
                                <div class="d-flex flex-column justify-content-center align-items-center position-relative" style="width: 120px; height: 120px;">
                                    <p class="mb-2">CGPA</p>
                                    <canvas id="gpaChart{{ $child->id }}"></canvas>
                                    <div class="position-absolute" style="transform: translate(-50%, -50%); top: 60%; left: 50%;">
                                        <span class="text-dark fw-bold">{{ $cgpa }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-center align-items-center position-relative" style="width: 120px; height: 120px;">
                                    <p class="mb-2">Credits</p>
                                    <canvas id="creditChart{{ $child->id }}"></canvas>
                                    <div class="position-absolute" style="transform: translate(-50%, -50%); top: 60%; left: 50%;">
                                        <span class="text-dark fw-bold d-flex align-items-center">{{ ($child->enrollments->count()) * 3 }}/ <span class="text-muted" style="font-size: 13px">144</span> </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-center align-items-center position-relative" style="width: 120px; height: 120px;">
                                    <p class="mb-2">Attendance</p>
                                    <canvas id="attendance{{ $child->id }}"></canvas>
                                    <div class="position-absolute" style="transform: translate(-50%, -50%); top: 60%; left: 50%;">
                                        <span class="text-dark fw-bold">{{ $child->attendance->where('date', '>=', '2025-08-01')->where('status', 'present')->count() }}%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    new Chart(document.getElementById('gpaChart{{ $child->id }}'), {
                                        type: 'doughnut',
                                        data: {
                                            datasets: [{
                                                data: [{{ $cgpa }}, {{ 4 - $cgpa }}],
                                                backgroundColor: ['#4CAF50', '#f0f0f0']
                                            }]
                                        },
                                        options: {
                                            cutout: '80%',
                                            plugins: {
                                                tooltip: { enabled: false },
                                                legend: { display: false }
                                            }
                                        }
                                    });

                                    new Chart(document.getElementById('creditChart{{ $child->id }}'), {
                                        type: 'doughnut',
                                        data: {
                                            datasets: [{
                                                data: [{{ ($child->enrollments->count()) * 3 }},  {{ 144 - (($child->enrollments->count()) * 3) }}],
                                                backgroundColor: ['#2196F3', '#f0f0f0']
                                            }]
                                        },
                                        options: {
                                            cutout: '80%',
                                            plugins: {
                                                tooltip: { enabled: false },
                                                legend: { display: false }
                                            }
                                        }
                                    });

                                    new Chart(document.getElementById('attendance{{ $child->id }}'), {
                                        type: 'doughnut',
                                        data: {
                                            datasets: [{
                                                data: [{{ $child->attendance->where('date', '>=', '2025-08-01')->where('status', 'present')->count() }}, {{ ($child->attendance->where('date', '>=', '2025-08-01')->count()) - ($child->attendance->where('date', '>=', '2025-08-01')->where('status', 'present')->count()) }}],
                                                backgroundColor: ['#FF9800', '#f0f0f0']
                                            }]
                                        },
                                        options: {
                                            cutout: '80%',
                                            plugins: {
                                                tooltip: { enabled: false },
                                                legend: { display: false }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
                    <a href="#" class="btn btn-primary mt-3" style="background-color: #002361;">View All Offers</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection