<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/admin') }}">Integration Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/catalog') ? 'active' : '' }}" href="{{ url('/admin/catalog') }}">Catalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/loan') ? 'active' : '' }}" href="{{ url('/admin/loan') }}">Loan Book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/reservation') ? 'active' : '' }}" href="{{ url('/admin/reservation') }}">Reservation Book</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @if (session('access_token'))
                        <li class="nav-item">
                            <span class="nav-link" style="color: white;">Hello, {{ $name }}</span>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/logout') }}">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
@stack('scripts')
</html>
