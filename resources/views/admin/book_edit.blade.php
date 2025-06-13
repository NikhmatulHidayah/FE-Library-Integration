@extends('layouts.admin-index')

@section('content')
<div class="container">
  <h2>Edit Book</h2>

  <form action="{{ route('book.update', $book['id']) }}" method="POST">
    @csrf
    <div class="form-group mb-2">
      <label>Title</label>
      <input type="text" name="title" value="{{ $book['title'] }}" class="form-control" required>
    </div>

    <div class="form-group mb-2">
      <label>Author</label>
      <input type="text" name="author" value="{{ $book['author'] }}" class="form-control" required>
    </div>

    <div class="form-group mb-2">
      <label>Category</label>
      <input type="text" name="category" value="{{ $book['category'] }}" class="form-control">
    </div>

    <div class="form-group mb-2">
      <label>Stock</label>
      <input type="number" name="stock" value="{{ $book['stock'] }}" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Update</button>
    <a href="{{ route('book.detail', $book['id']) }}" class="btn btn-secondary mt-3">Cancel</a>
  </form>
</div>
@endsection
