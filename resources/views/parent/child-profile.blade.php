@extends('layouts.parent')

@section('title', 'Child Profile')

@section('content')
    <h2 class="text-dark fw-bold mb-4 mt-3">{{ $child->name }}'s Profile</h2>
    <p><strong>ID:</strong> {{ $child->id }}</p>
    <p><strong>Major:</strong> {{ $child->major ?? 'General Education' }}</p>
    <p><strong>Year:</strong> {{ ucfirst($child->year) }}</p>
@endsection