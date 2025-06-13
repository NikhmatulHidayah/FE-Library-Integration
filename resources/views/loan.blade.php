@extends('layouts.index')

@section('content')
<div class="container">
  <h2 style="color:#626056"><strong>Loan Book</strong></h2>
    <div class="button-action">
        <a href="/catalog">
            <button type="button" class="btn btn-dark">Back to catalog</button>
        </a>
    </div>
  <div class="book-details" style="display: flex; align-items: center; gap: 20px; margin-top: 20px;">
    <div class="book-image" style="padding: 10px; background-color: #f3f3f3; border-radius: 10px; border: 1px solid #d1d1d1; width: 200px; height: 200px;">
      <img src="{{ !empty($book['images']) && count($book['images']) > 0 && !empty($book['images'][0]['image_url']) ? $book['images'][0]['image_url'] : 'https://st2.depositphotos.com/3687485/12226/v/950/depositphotos_122265864-stock-illustration-isometric-book-icon-vector-illustration.jpg' }}" alt="Book Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
    </div>

    <div class="book-info" style="flex: 2;">
      <h3>{{ $book['title'] }}</h3>

      <form method="POST" action="{{ route('loan.submit', $book['id']) }}">
        @csrf
        <div class="form-group">
          <label for="loan_date">Loan Date</label>
          <input type="date" name="loan_date" class="form-control" required value="{{ old('loan_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
        </div>

        <div class="form-group mt-3 mb-4">
          <label for="return_date">Return Date</label>
          <input type="date" name="return_date" class="form-control" required value="{{ old('return_date', \Carbon\Carbon::now()->addDays(7)->format('Y-m-d')) }}">
        </div>

        <button type="submit" class="btn btn-outline-dark mt-2">Submit Loan</button>
      </form>
    </div>
  </div>
  <br><br>
    <div class="alert alert-info mt-4">
      Please ensure that the book is returned in good condition and on or before the scheduled return date. Timely and proper return helps us maintain the quality of our collection and ensures availability for other users.
    </div>
</div>
@endsection
