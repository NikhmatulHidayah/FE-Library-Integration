@extends('layouts.index')

@section('content')
    <div class="container">
        <h2 style="color:#626056"><strong>Reservations Status</strong></h2>

        @if(count($reservations) > 0)
            <div class="row mt-4">
                @foreach($reservations as $reservation)
                    <div class="mb-2">
                        <div class="card" style="width: 100%; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                            <h5 class="card-title">{{ $reservation['book_title'] }}</h5> 
                            <p style="margin:0px;"><strong>Reservation Date:</strong> {{ $reservation['reservation_date'] }}</p>
                            <p><strong>Expire Date:</strong> {{ $reservation['expire_date'] }}</p>
                            <p><strong>Status:</strong> {{ $reservation['status'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No reservations found.</p>
        @endif
    </div>
@endsection
