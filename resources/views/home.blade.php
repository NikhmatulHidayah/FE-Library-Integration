@extends('layouts.index')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center">
        <h2 style="color:#626056;">
            <strong>List of Books</strong>
        </h2>
        <a href="/catalog">
            <button class="btn btn-outline-dark" style="font-size: 1rem;">Show More</button>
        </a>
    </div>

    <div class="container mt-4">
        <div class="row">
            @foreach ($books as $book)
                <div class="col-md-2 mb-3" style="padding-right: 12px; padding-left: 12px;">
                    <a href="{{ url('/catalog/book/' . $book['id']) }}" style="text-decoration: none;">
                        <div class="card" style="width: 100%; height: 325px;">
                            <img src="{{ !empty($book['images']) && !empty($book['images'][0]['image_url']) ? $book['images'][0]['image_url'] : 'https://st2.depositphotos.com/3687485/12226/v/950/depositphotos_122265864-stock-illustration-isometric-book-icon-vector-illustration.jpg' }}" class="card-img-top" alt="{{ $book['title'] }}" style="width: 100%; height: 150px; object-fit: cover;">

                            <div class="card-body" style="height: 175px; color:black;">
                                <h5 class="card-title" style="font-size: 1rem;">{{ $book['title'] }}</h5>
                                <p class="card-text" style="font-size: 0.9rem;">Author: {{ $book['author'] }}</p>
                                <p class="card-text" style="font-size: 0.9rem;">Category: {{ $book['category'] }}</p>
                                <p class="card-text" style="font-size: 0.9rem;">Stock: {{ $book['stock'] }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <br><br>
    <div class="d-flex justify-content-between align-items-center">
        <h2 style="color:#626056;">
            <strong>Course</strong>
            <small style="display: block; margin-top: 5px; font-size:15px;">Course powered by Learnup</small>
        </h2>
        <button disabled class="btn btn-outline-dark" style="font-size: 1rem;">Show More</button>
    </div>

    <div class="container mt-4">
        <div class="text-center">
            <img src="https://img.freepik.com/free-vector/maintenance-concept-illustration_114360-391.jpg?t=st=1749245540~exp=1749249140~hmac=9e6491b13a4c43eed5c312fc2f7c216327f4945f8b7a2ca706fccd18770ee006&w=1800" 
                 alt="Maintenance Concept" style="width: 300px; height: auto;">

            <p style="margin-top: 20px; font-size: 1.2rem; color: #626056;">
                Layanan ini sementara belum tersedia
            </p>
        </div>
    </div>

    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .row .col-md-2 {
            flex: 0 0 calc(20% - 12px);
        }

        .card-img-top {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .card-body {
            padding: 10px;
        }
    </style>

@endsection
