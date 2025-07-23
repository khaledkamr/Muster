@extends('layouts.admin')

@section('title', 'courses')

@section('content')
<style>
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-average {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-high {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .action-icons a,
    .action-icons form {
        display: inline-block;
        margin-right: 5px;
    }
    .action-icons i {
        font-size: 16px;
        color: #6c757d;
    }
    .action-icons i:hover {
        color: #007bff;
    }
    .action-icons .delete-icon:hover {
        color: #dc3545;
    }

    .action-icons form button {
        background: none;
        border: none;
        padding: 0;
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
<div class="container mt-4">
    <div class="row mb-2">
        <div class="col-md-3 mb-3">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-sitemap fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="card-title mb-0">General Education department</h5>
                        <h3 class="card-text">{{ $generalCourses }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-code fa-2x text-primary me-3"></i>
                    {{-- <i class="fas fa-user-graduate fa-2x text-primary me-3"></i> --}}
                    <div>
                        <h5 class="card-title mb-0">Computer Science department</h5>
                        <h3 class="card-text">{{ $CScourses }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-hexagon-nodes fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="card-title mb-0">Artificial Intelligence department</h5>
                        <h3 class="card-text">{{ $AIcourses }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <i class="fa-solid fa-database fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="card-title mb-0">Information System department</h5>
                        <h3 class="card-text">{{ $IScourses }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Top 5 Courses by Enrollments Chart -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Top 5 Courses by Enrollments</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="topCoursesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-light text-dark border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Course Difficulty Distribution</h5>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="difficultyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">Search for course:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control" placeholder="Search by Code or Name"
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn text-white fw-bold ms-2" style="background-color: #0A9442;">
                        Search
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Department:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="department" class="form-select" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('department') === 'all' || !request()->query('department') ? 'selected' : '' }}>
                            All departments</option>
                        <option value="General Education" {{ request()->query('department') === 'General Education' ? 'selected' : '' }}>
                            General Education</option>
                        <option value="Computer Science" {{ request()->query('department') === 'Computer Science' ? 'selected' : '' }}>
                            Computer Science</option>
                        <option value="Artificial Intelligence" {{ request()->query('department') === 'Artificial Intelligence' ? 'selected' : '' }}>
                            Artificial Intelligence</option>
                        <option value="Information System" {{ request()->query('department') === 'Information System' ? 'selected' : '' }}>
                            Information System</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="{{ route('admin.create.course') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-plus pe-1"></i>
                Add new Course
            </a>
        </div>
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

    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">Code</th>
                    <th class="text-center bg-dark text-white">Name</th>
                    <th class="text-center bg-dark text-white">Department</th>
                    <th class="text-center bg-dark text-white">Credit Hours</th>
                    <th class="text-center bg-dark text-white">Semester</th>
                    <th class="text-center bg-dark text-white">Type</th>
                    <th class="text-center bg-dark text-white">Difficulty</th>
                    <th class="text-center bg-dark text-white">Professor</th>
                    <th class="text-center bg-dark text-white">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($courses->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="status-danger fs-6">No courses found!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($courses as $course)
                        <tr>
                            <td class="text-center">{{ $course->code }}</td>
                            <td class="text-center">
                                <a href=""
                                    class="text-dark text-decoration-none">
                                    {{ $course->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $course->department }}</td>
                            <td class="text-center">{{ $course->credit_hours }}</td>
                            <td class="text-center">{{ $course->semester }}</td>
                            <td class="text-center">{{ $course->type }}</td>
                            <td class="text-center">{{ $course->difficulty }}</td>
                            <td class="text-center">{{ $course->professor->name }}</td>
                        
                            <td class="action-icons text-center">
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $course->id }}">
                                    <i class="fa-solid fa-pen text-primary" title="Edit course"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $course->id }}">
                                    <i class="fa-solid fa-trash-can text-danger" title="delete course"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editUserModal{{ $course->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $course->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold" id="editUserModalLabel{{ $course->id }}">Edit Course</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.update.course', $course->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <!-- Name and Code -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name{{ $course->id }}" class="form-label">Name</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <input type="text" class="form-control" id="name{{ $course->id }}" name="name" value="{{ old('name', $course->name) }}" required>
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="code{{ $course->id }}" class="form-label">Code</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <input type="text" class="form-control" id="code{{ $course->id }}" name="code" value="{{ old('code', $course->code) }}" required>
                                                    @error('code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Description (Full Width) -->
                                            <div class="mb-3">
                                                <label for="description{{ $course->id }}" class="form-label">Description</label>
                                                <span class="text-danger fw-bold">*</span>
                                                <textarea class="form-control" id="description{{ $course->id }}" name="description" rows="4" required>{{ old('description', $course->description) }}</textarea>
                                                @error('description')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Department and Credit Hours -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="department{{ $course->id }}" class="form-label">Department</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <select class="form-select" id="department{{ $course->id }}" name="department" required>
                                                        <option value="" disabled {{ old('department', $course->department) ? '' : 'selected' }}>Select department</option>
                                                        <option value="General Education" {{ old('department', $course->department) == 'General Education' ? 'selected' : '' }}>General Education</option>
                                                        <option value="Computer Science" {{ old('department', $course->department) == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                                        <option value="Artificial Intelligence" {{ old('department', $course->department) == 'Artificial Intelligence' ? 'selected' : '' }}>Artificial Intelligence</option>
                                                        <option value="Information System" {{ old('department', $course->department) == 'Information System' ? 'selected' : '' }}>Information System</option>
                                                    </select>
                                                    @error('department')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="credit_hours{{ $course->id }}" class="form-label">Credit Hours</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <input type="number" class="form-control" id="credit_hours{{ $course->id }}" name="credit_hours" value="{{ old('credit_hours', $course->credit_hours) }}" required min="1">
                                                    @error('credit_hours')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Semester and Type -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="semester{{ $course->id }}" class="form-label">Semester</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <select class="form-select" id="semester{{ $course->id }}" name="semester" required>
                                                        <option value="" disabled {{ old('semester', $course->semester) ? '' : 'selected' }}>Select semester</option>
                                                        <option value="first" {{ old('semester', $course->semester) == 'first' ? 'selected' : '' }}>First</option>
                                                        <option value="second" {{ old('semester', $course->semester) == 'second' ? 'selected' : '' }}>Second</option>
                                                    </select>
                                                    @error('semester')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="type{{ $course->id }}" class="form-label">Type</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <select class="form-select" id="type{{ $course->id }}" name="type" required>
                                                        <option value="" disabled {{ old('type', $course->type) ? '' : 'selected' }}>Select type</option>
                                                        <option value="compulsory" {{ old('type', $course->type) == 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                                                        <option value="elective" {{ old('type', $course->type) == 'elective' ? 'selected' : '' }}>Elective</option>
                                                    </select>
                                                    @error('type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Difficulty and Professor -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="difficulty{{ $course->id }}" class="form-label">Difficulty</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <select class="form-select" id="difficulty{{ $course->id }}" name="difficulty" required>
                                                        <option value="" disabled {{ old('difficulty', $course->difficulty) ? '' : 'selected' }}>Select difficulty</option>
                                                        <option value="easy" {{ old('difficulty', $course->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                                        <option value="medium" {{ old('difficulty', $course->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                        <option value="hard" {{ old('difficulty', $course->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                                    </select>
                                                    @error('difficulty')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="professor{{ $course->id }}" class="form-label">Professor</label>
                                                    <span class="text-danger fw-bold">*</span>
                                                    <select class="form-select" id="professor{{ $course->id }}" name="professor_id" required>
                                                        <option value="" disabled {{ old('professor_id', $course->professor_id) ? '' : 'selected' }}>Select professor</option>
                                                        @foreach ($professors as $professor)
                                                            <option value="{{ $professor->id }}" {{ old('professor_id', $course->professor_id) == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('professor_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteUserModal{{ $course->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $course->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold" id="deleteUserModalLabel{{ $course->id }}">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-dark">
                                        Are you sure you want to delete the course <strong>{{ $course->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.delete.course', $course->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $courses->appends(request()->query())->onEachSide(1)->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topCoursesChart = document.getElementById('topCoursesChart').getContext('2d');
        new Chart(topCoursesChart, {
            type: 'bar',
            data: {
                labels: @json($topFiveCourses['labels']),
                datasets: [{
                    label: 'students distribution',
                    data: @json($topFiveCourses['data']),
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: 20,
                    barThickness: 70,
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
                                return `Total: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.max(...@json($topFiveCourses['data'])) + 10,
                        ticks: {
                            stepSize: 30
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const difficultyCtx = document.getElementById('difficultyChart').getContext('2d');
        new Chart(difficultyCtx, {
            type: 'pie',
            data: {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [{
                    data: @json($difficultyDistribution),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)', 
                        'rgba(255, 193, 7, 0.7)', 
                        'rgba(220, 53, 69, 0.7)' 
                    ],
                    borderColor: [
                        '#fff',
                        '#fff',
                        '#fff',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'left',
                        labels: {
                            color: '#212529',
                            padding: 30
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} courses (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection