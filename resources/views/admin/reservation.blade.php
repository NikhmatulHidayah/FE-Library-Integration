@extends('layouts.admin-index')

@section('content')
    <div class="container">
        <h2 style="color:#626056"><strong>Reservations</strong></h2>

        @if(count($reservations) > 0)
            <div class="row mt-4">
                @foreach($reservations as $reservation)
                    <div class="mb-3">
                        <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <p style="margin:0px;"><strong>Title:</strong> {{ $reservation['book_title'] }}</p>
                            <p style="margin:0px;"><strong>Reservation Date:</strong> {{ $reservation['reservation_date'] }}</p>
                            <p style="margin:0px;"><strong>Expire Date:</strong> {{ $reservation['expire_date'] }}</p>
                            <p><strong>Status:</strong> {{ $reservation['status'] }}</p>
                            <p><strong>Book Stock:</strong> {{ $reservation['book_stock'] }}</p>
                            
                            @if($reservation['book_stock'] >= 1)
                                <form action="{{ route('admin.approveReservation', $reservation['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-50">Approve</button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-50" disabled>Not Available</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No reservations found.</p>
        @endif
    </div>
@endsection
