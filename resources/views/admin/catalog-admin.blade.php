@extends('layouts.admin-index')

@section('content')
    <h2 style="color:#626056;">
        <strong>Catalog of Books</strong>
    </h2>

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

    <form method="GET" action="{{ url('/admin/catalog') }}" class="mb-4">
        <div class="row">
            <div class="col-md-10 mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title, author, category..." />
            </div>
            
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-outline-dark w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="button-action">
        <a href="/admin/add/book">
            <button type="button" class="btn btn-dark">+ Add book</button>
        </a>
    </div>

    <div class="container mt-4">
        <div class="row">
            @foreach ($books as $book)
                <div class="col-md-2 mb-3" style="padding-right: 12px; padding-left: 12px;">
                    <a href="{{ url('/admin/catalog/book/' . $book['id']) }}" style="text-decoration: none;">
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

@endsection
