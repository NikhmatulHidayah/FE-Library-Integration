@extends('layouts.index')

@section('content')
  <div class="container">
    <h2 style="color:#626056">
        <strong>Book Detail</strong>
    </h2>
    <div class="button-action">
        <a href="/">
            <button type="button" class="btn btn-dark">Back to home</button>
        </a>
    </div>
    <br>

    @if($book)
      <div class="book-details" style="display: flex; align-items: center; gap: 20px; margin-top: 20px;">
        <div class="book-image" style="padding: 10px; background-color: #f3f3f3; border-radius: 10px; border: 1px solid #d1d1d1; width: 300px; height: 300px;">
            <img src="{{ !empty($book['images']) && count($book['images']) > 0 && !empty($book['images'][0]['image_url']) ? $book['images'][0]['image_url'] : 'https://st2.depositphotos.com/3687485/12226/v/950/depositphotos_122265864-stock-illustration-isometric-book-icon-vector-illustration.jpg' }}" alt="Book Image" style="width: 278px; height: 278px; object-fit: cover; border-radius: 8px;">
        </div>

        <div class="book-info" style="flex: 2;">
          <h2 style="padding: 10px; background-color: #f3f3f3; border-radius: 8px; border: 1px solid #d1d1d1;">{{ $book['title'] }}</h2>
          <p><strong>Author:</strong> {{ $book['author'] }}</p>
          <p><strong>Category:</strong> {{ $book['category'] ?? 'N/A' }}</p>
          <p><strong>Stock:</strong> {{ $book['stock'] }}</p>

          <a href="{{ $book['stock'] > 0 ? '/loan/book/' . $book['id'] : '#' }}" style="text-decoration: none;">
            <button type="button" class="btn btn-outline-dark" {{ $book['stock'] < 1 ? 'disabled' : '' }}>
              Loan this book
            </button>
          </a>

          @if($book['stock'] < 1)
            <a href="/reservation/book/{{ $book['id'] }}" style="margin-left: 10px;" style="text-decoration: none;">
              <button type="button" class="btn btn-outline-dark">Reservation this book</button>
            </a>
          @endif
        </div>
      </div>

      <br>

    @else
      <p>Book not found</p>
    @endif
  </div>
@endsection
