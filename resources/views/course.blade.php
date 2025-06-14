@extends('layouts.index')

@section('content')
    <h2 style="color:#626056;">
        <strong>All Courses</strong>
    </h2>

    <div class="container mt-4">
        <div class="row">
            @foreach ($courses as $course)
                <div class="col-md-3 mb-3" style="padding-right: 12px; padding-left: 12px;">
                    <a href="{{ url('/course/' . $course['id']) }}" style="text-decoration: none;">
                        <div class="card" style="width: 100%; height: 250px;">
                            <img src="https://foundr.com/wp-content/uploads/2021/09/Best-online-course-platforms.png" class="card-img-top" alt="{{ $course['title'] }}" style="width: 100%; height: 150px; object-fit: cover;">
                            <div class="card-body" style="height: 175px; color:black;">
                                <h5 class="card-title" style="font-size: 1rem;">{{ $course['title'] }}</h5>
                                <p class="card-text" style="font-size: 0.9rem;">{{ Str::limit($course['description'], 70) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

@endsection
