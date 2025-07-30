@extends('layouts.admin')

@section('title', 'Feedbacks')

@section('content')
<style>
    @media (max-width: 768px) {
        .filter-section {
            margin-top: 20px;
        }
    }
    .hover-animate {
        transition: all 0.3s ease-in-out;
    }
    .hover-animate:hover {
        transform: scale(1.2) rotate(-10deg);
    }
</style>

<div class="container-fluid py-4">
    <h2 class="text-dark fw-bold mb-4">Feedbacks</h2>
    <div class="row">
        <!-- Feedback List -->
        <div class="col-lg-8 col-md-12">
            <div class="search-bar d-flex align-items-center mb-4">
                <input type="text" id="search-input" class="form-control" placeholder="Search by sender or feedback...">
                <button class="btn btn-success ms-2">search</button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Operation failed
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div id="feedback-list">
                @foreach($feedbacks as $feedback)
                    <div class="position-relative bg-light rounded-3 shadow-sm p-3 mb-3" data-category="doctors" data-search="John Doe Dr. Smith Excellent consultation, very professional and caring.">
                        <p class="position-absolute bottom-0 start-0 small text-muted mb-3 ms-3">
                            {{ Carbon\Carbon::parse($feedback->date)->format('Y M d') }}
                            <i class="fa-solid fa-calendar-days"></i>
                        </p>
                        <button class="btn btn-link position-absolute top-0 end-0 small text-muted mt-2 me-2" type="button" data-bs-toggle="modal" data-bs-target="#deleteFeedbackModal{{ $feedback->id }}">
                            <i class="fa-solid fa-trash-can text-danger hover-animate" title="delete feedback"></i>
                        </button>
                        <p class="text-primary fw-bold m-0 p-0">
                            <i class="fa-solid fa-circle-user fa-lg text-muted"></i>
                            <a class="text-dark text-decoration-none" href="{{ route('admin.profile.user', $feedback->from) }}">{{ $feedback->sender->name }}</a>
                        </p>
                        <p class="text-muted">
                            about: 
                            <a class="text-muted text-decoration-none" href="{{ route('admin.profile.user', $feedback->about) }}">{{ $feedback->receiver->name }}</a>
                        </p>
                        <p class="text-dark">{{ $feedback->content }}</p>
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            @if($feedback->rate == 'excellent')
                                <span class="badge bg-success">{{ $feedback->rate }}</span>
                            @elseif($feedback->rate == 'good')
                                <span class="badge bg-primary">{{ $feedback->rate }}</span>
                            @elseif($feedback->rate == 'average')
                                <span class="badge bg-warning">{{ $feedback->rate }}</span>
                            @elseif($feedback->rate == 'bad')
                                <span class="badge bg-danger">{{ $feedback->rate }}</span>
                            @endif
                            
                            @if($feedback->course)    
                                <span class="badge bg-primary">{{ $feedback->course }}</span>
                            @endif

                            @if($feedback->receiver->role == 'professor')
                                <span class="badge bg-success">professor</span>
                            @elseif($feedback->receiver->role == 'student')
                                <span class="badge bg-primary">student</span>
                            @endif
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteFeedbackModal{{ $feedback->id }}" tabindex="-1" aria-labelledby="deleteFeedbackModalLabel{{ $feedback->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteFeedbackModalLabel{{ $feedback->id }}">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-dark">
                                    Are you sure you want to delete this feedback ?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.delete.feedback', $feedback->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Filter Section -->
        <div class="col-lg-4 col-md-12">
            <div class="bg-light rounded-3 p-4 shadow-sm">
                <h5 class="text-dark">Filter Feedback</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filter-doctors" value="doctors" checked>
                    <label class="text-dark" for="filter-doctors">Doctors</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filter-students" value="students" checked>
                    <label class="text-dark" for="filter-students">Students</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="filter-courses" value="courses" checked>
                    <label class="text-dark" for="filter-courses">Courses</label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter feedback based on checkbox selection
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateFeedbackDisplay();
        });
    });

    // Search bar functionality
    document.getElementById('search-input').addEventListener('input', function() {
        updateFeedbackDisplay();
    });

    function updateFeedbackDisplay() {
        const selectedCategories = [];
        document.querySelectorAll('.form-check-input:checked').forEach(checked => {
            selectedCategories.push(checked.value);
        });

        const searchQuery = document.getElementById('search-input').value.toLowerCase();

        const feedbackCards = document.querySelectorAll('.feedback-card');
        feedbackCards.forEach(card => {
            const category = card.getAttribute('data-category');
            const searchData = card.getAttribute('data-search').toLowerCase();
            const matchesCategory = selectedCategories.includes(category);
            const matchesSearch = searchQuery === '' || searchData.includes(searchQuery);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection