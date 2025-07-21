@extends('layouts.admin')

@section('title', 'users')

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
    .table .status-risk {
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
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">Search for user:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or Name"
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn text-white fw-bold ms-2" style="background-color: #0A9442;">
                        Search
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-5">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">Filter by Status:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="role" class="form-select" onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('role') === 'all' || !request()->query('role') ? 'selected' : '' }}>
                            All users</option>
                        <option value="professor" {{ request()->query('role') === 'professor' ? 'selected' : '' }}>
                            Professors</option>
                        <option value="admin" {{ request()->query('role') === 'admin' ? 'selected' : '' }}>
                            Admins</option>
                        <option value="student" {{ request()->query('role') === 'student' ? 'selected' : '' }}>
                            Students</option>
                        <option value="parent" {{ request()->query('role') === 'parent' ? 'selected' : '' }}>
                            Parents</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <a href="{{ route('admin.create.user') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-user-plus pe-1"></i>
                Add new user
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
                    <th class="text-center bg-dark text-white">ID</th>
                    <th class="text-center bg-dark text-white">Name</th>
                    <th class="text-center bg-dark text-white">Email</th>
                    <th class="text-center bg-dark text-white">Role</th>
                    <th class="text-center bg-dark text-white">Phone</th>
                    <th class="text-center bg-dark text-white">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ($users->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-refunded fs-6">No users found!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-center">{{ $user->id }}</td>
                            <td class="text-center">
                                <a href=""
                                    class="text-dark text-decoration-none">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $user->email }}</td>
                            <td class="text-center">{{ $user->role }}</td>
                            <td class="text-center">{{ $user->phone }}</td>
                        
                            <td class="action-icons text-center">
                                <a href="">
                                    <i class="fa-solid fa-message" title="send"></i>
                                </a>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-pen text-primary" title="Edit user"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-user-xmark text-danger" title="delete user"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.update.user', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="mb-3">
                                                <label for="name{{ $user->id }}" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="email{{ $user->id }}" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone{{ $user->id }}" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="phone{{ $user->id }}" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold" id="deleteUserModalLabel{{ $user->id }}">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-dark">
                                        Are you sure you want to delete the user <strong>{{ $user->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.delete.user', $user->id) }}" method="POST">
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
        {{ $users->appends(request()->query())->onEachSide(1)->links() }}
    </div>
</div>
@endsection