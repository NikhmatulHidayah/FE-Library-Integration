@extends('layouts.admin-index')

@section('content')
    <h2 style="color:#626056;">
        <strong>Add Book</strong>
    </h2>

    <div class="button-action">
        <a href="/admin/catalog">
            <button type="button" class="btn btn-dark">Back</button>
        </a>
    </div>
    <br>
    <form action="{{ route('admin.books.store') }}" method="POST" style="margin-top: 20px;" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-2">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="form-group mb-2">
            <label for="author">Author</label>
            <input type="text" name="author" id="author" class="form-control" required>
        </div>

        <div class="form-group mb-2">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" class="form-control" required>
        </div>

        <div class="form-group mb-2">
            <label for="stock">Stock</label>
            <input type="number" name="stock" id="stock" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            <small>Maksimal 2 MB</small>

        </div>

        <br><br>
        <button type="submit" class="btn btn-outline-dark">Add Book</button>
    </form>

@endsection
