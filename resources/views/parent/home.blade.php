@extends('layouts.parent')

@section('title', 'Home')

@section('content')
    <h2 class="text-dark fw-bold mb-4 mt-3">Welcome {{ Auth::user()->name }}</h2>
    <p class="text-muted">This is the parent dashboard home page. Manage your children's academic progress here.</p>
@endsection