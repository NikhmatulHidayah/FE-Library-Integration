@extends('layouts.admin-index')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mb-4">
            <div class="col-md-12">
                <label for="">Time</label>
                <p class="display-4" id="current-time">12:00:00</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card shadow-lg mb-4 rounded-3" style="background-color: #2c2f36; color: white;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Stock</h5>
                        <p class="card-text display-4">{{ $totalStock }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-lg mb-4 rounded-3" style="background-color: #444c56; color: white;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Loans</h5>
                        <p class="card-text display-4">{{ $totalLoans }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-lg mb-4 rounded-3" style="background-color: #555c66; color: white;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Members</h5>
                        <p class="card-text display-4">{{ $totalMembers }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-lg mb-4 rounded-3" style="background-color: #333840; color: white;">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="card-text display-4">{{ $totalReservations }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            var currentTime = new Date();
            var hours = currentTime.getHours();
            var minutes = currentTime.getMinutes();
            var seconds = currentTime.getSeconds();

            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            var timeString = hours + ':' + minutes + ':' + seconds;
            document.getElementById('current-time').innerText = timeString;
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
@endsection
