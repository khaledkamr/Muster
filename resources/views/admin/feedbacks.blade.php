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
    .pagination {
        margin: 0;
        padding: 0;
    }
    .pagination .page-item .page-link {
        color: #0A9442;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        margin: 0 2px;
        border-radius: 4px;
        transition: 0.3s;
    }
    .pagination .page-item.active .page-link {
        background-color: #0A9442;
        border-color: #0A9442;
        color: white;
    }
    .pagination .page-item .page-link:hover {
        background-color: #0A9442;
        border-color: #0A9442;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #fff;
        pointer-events: none;
        background-color: #d5d7d8;
        border-color: #dee2e6;
    }
</style>

<div class="container-fluid py-4">
    <h2 class="text-dark fw-bold mb-4">Feedbacks</h2>
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <form method="GET" action="">
                <div class="search-bar d-flex align-items-center mb-4">
                    <input type="text" name="search" class="form-control" placeholder="Search for feedback..."
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-success ms-2">search</button>
                </div>
            </form>

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
                    <div class="position-relative bg-light rounded-3 shadow-sm p-3 mb-3">
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
            <div class="mt-4">
                {{ $feedbacks->appends(request()->query())->onEachSide(1)->links() }}
            </div>
        </div>

        <!-- Filter Section -->
        <div class="col-lg-4 col-md-12">
            <div class="bg-light rounded-3 p-4 shadow-sm">
                <h5 class="text-dark mb-3">Feedback Distribution</h5>
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <div class="me-3 d-flex align-items-center">
                        <span class="me-1 rounded-circle bg-primary d-block" style="width: 15px; height: 15px;"></span>
                        <span class="text-muted d-block fs-6">students</span>
                    </div>
                    <div class="me-3 d-flex align-items-center">
                        <span class="me-1 rounded-circle bg-success d-block" style="width: 15px; height: 15px;"></span>
                        <span class="text-muted d-block fs-6">professors</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-1 rounded-circle bg-warning d-block" style="width: 15px; height: 15px;"></span>
                        <span class="text-muted d-block fs-6">courses</span>
                    </div>
                </div>
                <div class="progress-stacked mb-5" style="height: 30px">
                    <div class="progress" role="progressbar" aria-label="Segment one" aria-valuenow="{{ $professorsFeedback }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $professorsFeedback }}%; height: 30px">
                        <div class="progress-bar bg-primary fw-bold"> {{ $professorsFeedback }}% </div>
                    </div>
                    <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="{{ $studentsFeedback }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $studentsFeedback }}%; height: 30px">
                        <div class="progress-bar bg-success fw-bold"> {{ $studentsFeedback }}% </div>
                    </div>
                    <div class="progress" role="progressbar" aria-label="Segment three" aria-valuenow="{{ $coursesFeedback }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $coursesFeedback }}%; height: 30px">
                        <div class="progress-bar bg-warning fw-bold"> {{ $coursesFeedback }}% </div>
                    </div>
                </div>

                <h5 class="text-dark mb-3">Filter Feedbacks</h5>
                <form method="GET" action="" class="d-flex flex-column mb-3">
                    <div class="d-flex">
                        <select id="filter" name="filter" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ request()->query('filter') == 'all' ? 'selected' : ''}}>all</option>
                            <option value="professor" {{ request()->query('filter') == 'professor' ? 'selected' : ''}}>
                                professors feedbacks
                            </option>
                            <option value="student" {{ request()->query('filter') == 'student' ? 'selected' : ''}}>
                                students feedbacks
                            </option>
                            <option value="courses" {{ request()->query('filter') == 'courses' ? 'selected' : ''}}>
                                courses feedbacks
                            </option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection