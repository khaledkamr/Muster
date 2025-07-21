@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="container">
    <h1 class="text-dark fw-bold pt-3 pb-4">Create New User</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="col-md-12 mx-auto">
        <form action="{{ route('admin.store.user') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm text-white mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="role" class="form-label text-dark">User Type</label>
                        <span class="text-danger fw-bold">*</span>
                        <select class="form-select" id="role" name="role">
                            <option value="" disabled selected>Select user type</option>
                            <option value="student">Student</option>
                            <option value="professor">Professor</option>
                            <option value="parent">Parent</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Common Fields (Two Inputs in Line) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label text-dark">Name</label>
                            <span class="text-danger fw-bold">*</span>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label text-dark">Email</label>
                            <span class="text-danger fw-bold">*</span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label text-dark">Password</label>
                            <span class="text-danger fw-bold">*</span>
                            <input type="password" class="form-control" id="password" name="password">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role_display" class="form-label text-dark">Role</label>
                            <input type="text" class="form-control" id="role_display" value="{{ old('role', 'Select user type above') }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="gender" class="form-label text-dark">Gender</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="gender" name="gender">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label text-dark">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="birthdate" class="form-label text-dark">Birthdate</label>
                        <span class="text-danger fw-bold">*</span>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
                        @error('birthdate')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Student-Specific Fields -->
                    <div class="student-fields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="year" class="form-label text-dark">Year</label>
                                <span class="text-danger fw-bold">*</span>
                                <select class="form-select" id="year" name="year">
                                    <option value="" disabled {{ old('year') ? '' : 'selected' }}>Select year</option>
                                    <option value="freshman" {{ old('year') == 'freshman' ? 'selected' : '' }}>Freshman</option>
                                    <option value="sophomore" {{ old('year') == 'sophomore' ? 'selected' : '' }}>Sophomore</option>
                                    <option value="junior" {{ old('year') == 'junior' ? 'selected' : '' }}>Junior</option>
                                    <option value="senior" {{ old('year') == 'senior' ? 'selected' : '' }}>Senior</option>
                                </select>
                                @error('year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="parent_id" class="form-label text-dark">Parent</label>
                                <span class="text-danger fw-bold">*</span>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="" {{ old('parent_id') ? '' : 'selected' }}>Select parent</option>
                                    <option value="">No parent</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Professor-Specific Fields -->
                    <div class="professor-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="department" class="form-label text-dark">Department</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="department" name="department">
                                <option value="" disabled {{ old('department') ? '' : 'selected' }}>Select department</option>
                                <option value="General Education" {{ old('department') == 'General Education' ? 'selected' : '' }}>General Education</option>
                                <option value="Computer Science" {{ old('department') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                                <option value="Artificial Intelligence" {{ old('department') == 'Artificial Intelligence' ? 'selected' : '' }}>Artificial Intelligence</option>
                                <option value="Information System" {{ old('department') == 'Information System' ? 'selected' : '' }}>Information System</option>
                            </select>
                            @error('department')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const roleDisplay = document.getElementById('role_display');
        const studentFields = document.querySelector('.student-fields');
        const professorFields = document.querySelector('.professor-fields');

        roleSelect.addEventListener('change', function () {
            // Update role display
            roleDisplay.value = this.value || 'Select user type above';

            // Show/hide fields based on role
            studentFields.style.display = this.value === 'student' ? 'block' : 'none';
            professorFields.style.display = this.value === 'professor' ? 'block' : 'none';

            // Update required attributes
            document.querySelectorAll('.student-fields select, .professor-fields select').forEach(field => {
                field.required = field.closest('.student-fields, .professor-fields').style.display === 'block';
            });
        });

        // Trigger change on page load to handle old input
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection