@extends('layouts.admin-index')

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
  <div class="container">
    <h2 style="color:#626056">
        <strong>Book Detail</strong>
    </h2>
    <div class="button-action">
        <a href="/admin/catalog">
            <button type="button" class="btn btn-dark">Back</button>
        </a>
    </div>

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
          <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($book['created_at'])->format('d-m-Y H:i:s') }}</p>
          <p><strong>Updated At:</strong> {{ \Carbon\Carbon::parse($book['updated_at'])->format('d-m-Y H:i:s') }}</p>
        </div>
      </div>

      <br>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <a href="{{ route('book.edit', $book['id']) }}">
                <button class="btn btn-outline-dark" style="width:150px;">Edit</button>
            </a>
            <form action="{{ route('book.delete', $book['id']) }}" method="POST" style="display: inline-block;">
              @csrf
              @method('DELETE')
                <button class="btn btn-danger" style="width:150px;">Delete</button>
            </form>
        </div>

    @else
      <p>Book not found</p>
    @endif
  </div>
@endsection
