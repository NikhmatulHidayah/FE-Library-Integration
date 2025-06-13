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
                                    <p style="margin: 0px;"><strong>Rate the book:</strong></p>
                                    <div class="stars">
                                        <span class="star" data-rating="1">&#9733;</span>
                                        <span class="star" data-rating="2">&#9733;</span>
                                        <span class="star" data-rating="3">&#9733;</span>
                                        <span class="star" data-rating="4">&#9733;</span>
                                        <span class="star" data-rating="5">&#9733;</span>
                                    </div>
                                    <p><strong>Leave a comment:</strong></p>
                                    <textarea id="comment" name="comment" class="form-control" rows="4" placeholder="Your comment here..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <form id="reviewForm" action="{{ route('review.submit', $loan['id']) }}" method="POST">
                                        @csrf
                                        <input type="hidden" id="rating" name="rating" value="">
                                        <input type="hidden" name="book_id" value="{{ $loan['book_id'] }}">
                                        <input type="hidden" id="hiddenComment" name="comment" value="">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-outline-dark">Submit Review</button>
                                    </form>
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
        const stars = document.querySelectorAll('.star');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', () => {
                selectedRating = star.getAttribute('data-rating');
                document.getElementById('rating').value = selectedRating;
                stars.forEach(s => {
                    s.classList.remove('selected');
                });
                for (let i = 0; i < selectedRating; i++) {
                    stars[i].classList.add('selected');
                }
            });
        });

        const reviewForm = document.getElementById('reviewForm');
        reviewForm.addEventListener('submit', function() {
            const commentText = document.getElementById('comment').value;
            document.getElementById('hiddenComment').value = commentText;
        });
    </script>

    <style>
        .star {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
        }

        .star.selected {
            color: gold;
        }

        .star:hover {
            color: gold;
        }
    </style>
@endpush
