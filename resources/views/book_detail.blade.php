@extends('layouts.index')

@section('content')
  <div class="container">
    <h2 style="color:#626056"><strong>Book Detail</strong></h2>

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
            <!-- Modal Trigger Button -->
            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#reservationModal">
              Reservation this book
            </button>
          @endif
        </div>
      </div>

      <br>

    @else
      <p>Book not found</p>
    @endif
  </div>
  <br><br>
  <div class="container">
      <h2 style="color:#626056; font-size:20px;">Review</h2>

      @foreach($reviews as $review)
          <div class="review-box" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
              <div class="review-header" style="display: flex; align-items: center; margin-bottom: 10px;">
                  <div class="rating" style="margin-right: 15px;">
                      @for ($i = 1; $i <= 5; $i++)
                          <span class="star" style="font-size: 20px; color: {{ $i <= $review['rating'] ? 'gold' : '#ddd' }};">&#9733;</span>
                      @endfor
                  </div>
              </div>
              <div class="review-comment">
                  <p><strong>Comment:</strong> {{ $review['comment'] }}</p>
              </div>
              <div class="review-footer" style="text-align: right; font-size: 12px; color: #777;">
                  <small>Reviewed at: {{ \Carbon\Carbon::parse($review['created_at'])->format('M d, Y') }}</small>
              </div>
          </div>
      @endforeach
  </div>

  <div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reservationModalLabel">Book Reservation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ url('/reservation/book/' . $book['id']) }}">
            @csrf
            <div class="form-group mb-2">
              <label for="reservation_date">Reservation Date</label>
              <input type="date" class="form-control" id="reservation_date" name="reservation_date" required>
            </div>
            <div class="form-group mb-4">
              <label for="expire_date">Expiration Date</label>
              <input type="date" class="form-control" id="expire_date" name="expire_date" required>
            </div>
            <button type="submit" class="btn btn-outline-dark">Confirm Reservation</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
