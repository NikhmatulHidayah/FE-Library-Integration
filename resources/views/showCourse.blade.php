@extends('layouts.index')

@section('content')
    <div class="container mt-4">
        <h2 style="color:#626056;">
            <strong>{{ $course['title'] }}</strong>
        </h2>
        <div class="button-action">
        <a href="/course/all">
            <button type="button" class="btn btn-dark">Back to course</button>
        </a>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Description</h5>
                        <p class="card-text">{{ $course['description'] }}</p>

                        <h5 class="card-title mt-4">Content</h5>
                        <p class="card-text">{{ $course['content'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
