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

    <div class="row mt-4">
        <h3 style="color:#626056;"><strong>Active Loans</strong></h3>
        <br><br>
        @foreach($activeLoans as $loan)
            <div class="col-md-4 mb-3">
                <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                    <h5 class="card-title">{{ $loan['title'] }}</h5>
                    <br>
                    <p style="margin: 0px;"><strong>Loan Date:</strong> {{ $loan['loan_date'] }}</p>
                    <p><strong>Return Date:</strong> {{ $loan['return_date'] }}</p>

                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#returnBookModal{{ $loan['id'] }}">
                        Return Book
                    </button>

                    <div class="modal fade" id="returnBookModal{{ $loan['id'] }}" tabindex="-1" aria-labelledby="returnBookModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="returnBookModalLabel">Confirm Book Return</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Please make sure the book is in good condition before returning it.</strong></p>
                                    <ul>
                                        <li>Check if the book is not damaged.</li>
                                        <li>Ensure all pages are intact and undamaged.</li>
                                        <li>Return the book in the same condition it was borrowed.</li>
                                    </ul>
                                    <p>Are you sure you want to return this book?</p>
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('loan.return', $loan['id']) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning">Return Book</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <h3 style="color:#626056;"><strong>Loan History</strong></h3>
        <br><br>
        @foreach($returnedLoans as $loan)
            <div class="col-md-4 mb-3">
                <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                    <h5 class="card-title">{{ $loan['title'] }}</h5>
                    <br>
                    <p style="margin: 0px;"><strong>Loan Date:</strong> {{ $loan['loan_date'] }}</p>
                    <p><strong>Return Date:</strong> {{ $loan['return_date'] }}</p>
                    <p><strong>Status:</strong> {{ $loan['status'] }}</p>

                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#reviewBookModal{{ $loan['id'] }}">
                        Review Book
                    </button>

                    <div class="modal fade" id="reviewBookModal{{ $loan['id'] }}" tabindex="-1" aria-labelledby="reviewBookModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reviewBookModalLabel">Give a Review for {{ $loan['title'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('review.submit', $loan['id']) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $loan['book_id'] }}">
                                        
                                        <p style="margin:0px;"><strong>Rate the book:</strong></p>
                                        <input type="number" name="rating" min="1" max="5" step="1" class="form-control mb-2" placeholder="1-5" required>

                                        <p style="margin:0px;"><strong>Leave a comment:</strong></p>
                                        <textarea name="comment" class="form-control" rows="4" placeholder="Your comment here..." required></textarea>

                                        <br>
                                        <button type="submit" class="btn btn-outline-dark">Submit Review</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cancelButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
            cancelButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const ratings = document.querySelectorAll('[id^="rating"]');
                    ratings.forEach(rating => {
                        rating.value = '';
                    });
                    const comments = document.querySelectorAll('[id^="comment"]');
                    comments.forEach(comment => {
                        comment.value = '';
                    });
                });
            });
        });
    </script>
@endpush
