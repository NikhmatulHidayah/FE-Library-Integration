@extends('layouts.admin-index')

@section('content')
    <div class="container">
        <h2 style="color:#626056"><strong>Loan Status</strong></h2>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @else
            <div class="row mt-4">
                <h4>Borrowed Loans</h4>
                @foreach($groupedLoans['borrowed'] as $loan)
                    <div class="col-md-4 mb-3">
                        <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <p style="margin:0px;"><strong>Book Title:</strong> {{ $loan['book_title'] }}</p>
                            <p style="margin:0px;"><strong>User Name:</strong> {{ $loan['user_name'] }}</p>
                            <p><strong>Loan Date:</strong> {{ $loan['loan_date'] }}</p>
                            <p style="margin:0px;"><strong>Return Date:</strong> {{ $loan['return_date'] }}</p>
                            <p style="margin:0px;"><strong>Status:</strong> {{ $loan['status'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <h4>Late Loans</h4>
                @foreach($groupedLoans['late'] as $loan)
                    <div class="col-md-4 mb-3">
                        <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <p style="margin:0px;"><strong>Book Title:</strong> {{ $loan['book_title'] }}</p>
                            <p style="margin:0px;"><strong>User Name:</strong> {{ $loan['user_name'] }}</p>
                            <p><strong>Loan Date:</strong> {{ $loan['loan_date'] }}</p>
                            <p style="margin:0px;"><strong>Return Date:</strong> {{ $loan['return_date'] }}</p>
                            <p style="margin:0px;"><strong>Status:</strong> {{ $loan['status'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <h4>Returned Loans</h4>
                @foreach($groupedLoans['returned'] as $loan)
                    <div class="col-md-4 mb-3">
                        <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <p style="margin:0px;"><strong>Book Title:</strong> {{ $loan['book_title'] }}</p>
                            <p style="margin:0px;"><strong>User Name:</strong> {{ $loan['user_name'] }}</p>
                            <p><strong>Loan Date:</strong> {{ $loan['loan_date'] }}</p>
                            <p style="margin:0px;"><strong>Return Date:</strong> {{ $loan['return_date'] }}</p>
                            <p style="margin:0px;"><strong>Status:</strong> {{ $loan['status'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
@endpush
