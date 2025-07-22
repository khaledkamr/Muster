@extends('layouts.admin')

@section('title', 'Create Course')

@section('content')
<div class="container">
    <h1 class="text-dark fw-bold pt-3 pb-4">Create New Course</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="col-md-12 mx-auto">
        <form action="{{ route('admin.store.course') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm text-white mb-4">
                <div class="card-body">
                    <!-- Name and Code -->
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
                            <label for="code" class="form-label text-dark">Code</label>
                            <span class="text-danger fw-bold">*</span>
                            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}">
                            @error('code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description (Full Width) -->
                    <div class="mb-3">
                        <label for="description" class="form-label text-dark">Description</label>
                        <span class="text-danger fw-bold">*</span>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department and Credit Hours -->
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <label for="credit_hours" class="form-label text-dark">Credit Hours</label>
                            <span class="text-danger fw-bold">*</span>
                            <input type="number" class="form-control" id="credit_hours" name="credit_hours" value="{{ old('credit_hours') }}" min="1">
                            @error('credit_hours')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Semester and Type -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="semester" class="form-label text-dark">Semester</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="semester" name="semester">
                                <option value="" disabled {{ old('semester') ? '' : 'selected' }}>Select semester</option>
                                <option value="first" {{ old('semester') == 'first' ? 'selected' : '' }}>First</option>
                                <option value="second" {{ old('semester') == 'second' ? 'selected' : '' }}>Second</option>
                            </select>
                            @error('semester')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label text-dark">Type</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="type" name="type">
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select type</option>
                                <option value="compulsory" {{ old('type') == 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                                <option value="elective" {{ old('type') == 'elective' ? 'selected' : '' }}>Elective</option>
                            </select>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Difficulty and Professor -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="difficulty" class="form-label text-dark">Difficulty</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="" disabled {{ old('difficulty') ? '' : 'selected' }}>Select difficulty</option>
                                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                            @error('difficulty')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="professor_id" class="form-label text-dark">Professor</label>
                            <span class="text-danger fw-bold">*</span>
                            <select class="form-select" id="professor_id" name="professor_id">
                                <option value="" disabled {{ old('professor_id') ? '' : 'selected' }}>Select professor</option>
                                @foreach ($professors as $professor)
                                    <option value="{{ $professor->id }}" {{ old('professor_id') == $professor->id ? 'selected' : '' }}>{{ $professor->name }}</option>
                                @endforeach
                            </select>
                            @error('professor_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection