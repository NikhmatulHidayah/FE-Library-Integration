<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use GraphQL;
use Carbon\Carbon;
use Exception;


class AdminController extends Controller
{
    public function checkAdmin()
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        return $name;
    }
    public function showAdminHome()
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        $client = new Client();

        $bookQuery = <<<GQL
        query {
            books {
                id
                title
                author
                category
                stock
                created_at
                updated_at
                images {
                    id
                    image_url
                }
            }
        }
        GQL;

        $bookResponse = $client->post('http://localhost:8082/graphql', [
            'json' => ['query' => $bookQuery]
        ]);

        $bookData = json_decode($bookResponse->getBody()->getContents(), true);
        $books = $bookData['data']['books'];

        $totalStock = array_reduce($books, function ($carry, $book) {
            return $carry + $book['stock'];
        }, 0);

        $loanQuery = <<<GQL
        query {
            allLoans {
                id
                user_id
                book_id
                loan_date
                return_date
                status
            }
        }
        GQL;

        $loanResponse = $client->post('http://localhost:8085/graphql', [
            'json' => ['query' => $loanQuery]
        ]);
        $loanData = json_decode($loanResponse->getBody()->getContents(), true);
        $totalLoans = count($loanData['data']['allLoans']);

        $userQuery = <<<GQL
        query {
            users {
                id
                name
                email
                role
                created_at
                updated_at
            }
        }
        GQL;

        $userResponse = $client->post('http://localhost:8081/graphql', [
            'json' => ['query' => $userQuery]
        ]);
        $userData = json_decode($userResponse->getBody()->getContents(), true);
        $totalMembers = count(array_filter($userData['data']['users'], function ($user) {
            return $user['role'] === 'student';
        }));

        $reservationQuery = <<<GQL
        query {
            getAllReservations {
                id
                user_id
                book_id
                reservation_date
                status
                expire_date
                created_at
                updated_at
            }
        }
        GQL;

        $reservationResponse = $client->post('http://localhost:8087/graphql', [
            'json' => ['query' => $reservationQuery]
        ]);
        $reservationData = json_decode($reservationResponse->getBody()->getContents(), true);
        $totalReservations = count($reservationData['data']['getAllReservations']);

        return view('admin.home', [
            'name' => $name,
            'totalStock' => $totalStock,
            'totalLoans' => $totalLoans,
            'totalMembers' => $totalMembers,
            'totalReservations' => $totalReservations,
        ]);
    }

    public function showAdminCatalog(Request $request){
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        $client = new Client();

        $keyword = $request->input('search', '');

        if ($keyword) {
            $query = <<<GQL
            query {
                searchBooks(keyword: "$keyword") {
                    id
                    title
                    author
                    category
                    stock
                    images {
                        image_url
                    }
                }
            }
            GQL;
        } else {
            $query = <<<GQL
            query {
                books {
                    id
                    title
                    author
                    category
                    stock
                    images {
                        image_url
                    }
                }
            }
            GQL;
        }

        $response = $client->post('http://localhost:8082/graphql', [
            'json' => ['query' => $query]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $books = isset($data['data']['searchBooks']) ? $data['data']['searchBooks'] : (isset($data['data']['books']) ? $data['data']['books'] : []);

        //dd($books);
        return view('admin.catalog-admin', ['books' => $books, 'name' => $name]);
    }

    public function showAdminAddBook(){
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        return view('admin.addbook', ['name' => $name]);
    }

    public function store(Request $request)
    {
        $token = session('access_token');

        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'category' => 'required|string',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]); 

        $image = $request->file('image');   
        $imageUrl = $this->uploadImageToImgBB($image);

        $data = [
            'query' => '
                mutation {
                    createBook(input: {
                        title: "' . $request->title . '"
                        author: "' . $request->author . '"
                        category: "' . $request->category . '"
                        stock: ' . $request->stock . '
                    }) {
                        id
                        title
                        author
                        category
                        stock
                    }
                }
            ',
        ];     

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('http://localhost:8082/graphql', $data);   

        //dd($response);  

        if ($response->successful()) {
            $bookId = $response->json()['data']['createBook']['id'];

            Http::post('http://localhost:8082/api/upload-book-image', [
                'book_id' => $bookId,
                'image_url' => $imageUrl,
            ]); 

            return redirect('/admin/catalog')->with('success', 'Book added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add book. Please try again.');
        }
    }

    public function show($id)
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        try {
            $query = '
                query {
                  getBookById(id: "' . $id . '") {
                    id
                    title
                    author
                    category
                    stock
                    created_at
                    updated_at
                    images {
                      id
                      image_url
                    }
                  }
                }
            ';
            
            $response = Http::post('http://localhost:8082/graphql', [
                'query' => $query
            ]);

            $data = $response->json();

            if (isset($data['errors'])) {
                return abort(404, 'Book not found');
            }

            $book = $data['data']['getBookById'];

            return view('admin.book_detail', ['name' => $name, 'book' => $book]);

        } catch (Exception $e) {
            return abort(500, 'Error fetching data: ' . $e->getMessage());
        }
    }
    private function uploadImageToImgBB($image)
    {
        $apiKey = 'db7b3539b3aa0fe22d45cdf248e4b2fc';

        $response = Http::attach('image', file_get_contents($image), 'image.jpg')
            ->post('https://api.imgbb.com/1/upload?key=' . $apiKey);

        if ($response->successful()) {
            return $response->json()['data']['url'];
        }

        dd($response);

        return null;
    }

    public function edit($id)
    {
        $name = $this->checkAdmin();
        if (is_string($name)) {
            $query = '
                query {
                  getBookById(id: "' . $id . '") {
                    id
                    title
                    author
                    category
                    stock
                  }
                }
            ';

            $token = session('access_token');
            $response = Http::withToken($token)->post('http://localhost:8082/graphql', [
                'query' => $query,
            ]);

            $data = $response->json();

            if (isset($data['errors'])) {
                return abort(404, 'Book not found');
            }

            $book = $data['data']['getBookById'];
            return view('admin.book_edit', compact('book', 'name'));
        }
    }

    public function update(Request $request, $id)
    {
        $name = $this->checkAdmin();
        if (is_string($name)) {
            $validated = $request->validate([
                'title' => 'required|string',
                'author' => 'required|string',
                'category' => 'nullable|string',
                'stock' => 'required|integer',
            ]);

            $mutation = '
                mutation {
                  updateBook(id: ' . $id . ', input: {
                    title: "' . addslashes($validated['title']) . '",
                    author: "' . addslashes($validated['author']) . '",
                    category: "' . addslashes($validated['category'] ?? '') . '",
                    stock: ' . $validated['stock'] . '
                  }) {
                    id
                  }
                }
            ';

            $token = session('access_token');
            $response = Http::withToken($token)->post('http://localhost:8082/graphql', [
                'query' => $mutation,
            ]);

            $result = $response->json();

            if (isset($result['errors'])) {
                return back()->withErrors('Failed to update the book.');
            }

            return redirect()->route('book.detail', $id)->with('success', 'Book updated successfully.');
        }

        return $name;
    }

    public function destroy($id)
    {
        $name = $this->checkAdmin();
        if (is_string($name)) {
            $mutation = '
                mutation {
                  deleteBook(id: ' . $id . ')
                }
            ';

            $token = session('access_token');
            $response = Http::withToken($token)->post('http://localhost:8082/graphql', [
                'query' => $mutation,
            ]);

            $result = $response->json();

            if (isset($result['errors'])) {
                return back()->withErrors('Failed to delete the book.');
            }

            return redirect('/admin/catalog')->with('success', 'Book deleted successfully.');
        }

        return $name;
    }

    public function loan()
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        $response = Http::withToken($token)->post('http://localhost:8085/graphql', [
            'query' => '
                query {
                    allLoans {
                        id
                        user_id
                        book_id
                        loan_date
                        return_date
                        status
                    }
                }
            '
        ]);

        if ($response->failed()) {
            return view('admin.loan', ['error' => 'Failed to fetch loan data.']);
        }

        $loans = $response->json()['data']['allLoans'];

        $groupedLoans = [
            'borrowed' => [],
            'late' => [],
            'returned' => [],
        ];

        $today = Carbon::now()->toDateString();

        foreach ($loans as $loan) {
            if ($loan['status'] !== 'returned' && Carbon::parse($loan['return_date'])->lt($today)) {
                $this->updateLoanStatus($loan['id'], 'late');
            }

            $bookResponse = Http::post('http://localhost:8082/graphql', [
                'query' => '
                    query {
                        getBookById(id: "' . $loan['book_id'] . '") {
                            id
                            title
                        }
                    }
                '
            ]);

            $bookData = $bookResponse->json()['data']['getBookById'];
            $loan['book_title'] = $bookData ? $bookData['title'] : 'Unknown Title';

            $userResponse = Http::post('http://localhost:8081/graphql', [
                'query' => '
                    query {
                        user(id: "' . $loan['user_id'] . '") {
                            id
                            name
                        }
                    }
                '
            ]);

            $userData = $userResponse->json()['data']['user'];
            $loan['user_name'] = $userData ? $userData['name'] : 'Unknown User';

            if ($loan['status'] == 'borrowed') {
                $groupedLoans['borrowed'][] = $loan;
            } elseif ($loan['status'] == 'late') {
                $groupedLoans['late'][] = $loan;
            } elseif ($loan['status'] == 'returned') {
                $groupedLoans['returned'][] = $loan;
            }
        }

        return view('admin.loan', ['groupedLoans' => $groupedLoans, 'name' => $name]);
    }

    private function updateLoanStatus($loanId, $status)
    {
        $token = session('access_token');

        $response = Http::withToken($token)->post('http://localhost:8085/graphql', [
            'query' => '
                mutation {
                    updateLoanStatus(id: ' . $loanId . ', status: ' . $status . ') {
                        id
                        status
                        updated_at
                    }
                }
            '
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to update loan status to " . $status);
        }
    }

    public function reservation()
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        $client = new Client();

        $reservationQuery = <<<GQL
        query {
            getAllReservations {
                id
                user_id
                book_id
                reservation_date
                status
                expire_date
                created_at
                updated_at
            }
        }
        GQL;

        $reservationResponse = $client->post('http://localhost:8087/graphql', [
            'json' => ['query' => $reservationQuery]
        ]);

        $reservationData = json_decode($reservationResponse->getBody()->getContents(), true);

        if (isset($reservationData['errors'])) {
            return abort(500, 'Failed to fetch reservation data.');
        }

        $reservations = $reservationData['data']['getAllReservations'];

        $pendingReservations = array_filter($reservations, function ($reservation) {
            return $reservation['status'] === 'pending';
        });

        foreach ($pendingReservations as &$reservation) {
            $bookResponse = $client->post('http://localhost:8082/graphql', [
                'json' => [
                    'query' => '
                        query {
                            getBookById(id: "' . $reservation['book_id'] . '") {
                                id
                                title
                                stock
                            }
                        }
                    '
                ]
            ]);

            $bookData = json_decode($bookResponse->getBody()->getContents(), true);

            if (isset($bookData['data']['getBookById'])) {
                $reservation['book_title'] = $bookData['data']['getBookById']['title'];
                $reservation['book_stock'] = $bookData['data']['getBookById']['stock'];
            } else {
                $reservation['book_title'] = 'Unknown Title';
                $reservation['book_stock'] = 0;
            }
        }

        return view('admin.reservation', [
            'reservations' => $pendingReservations,
            'name' => $name
        ]);
    }

    public function approveReservation($id)
    {
        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'admin') {
            return redirect('/logout');
        }

        $mutationQuery = <<<GQL
        mutation {
            updateReservationStatus(id: $id, status: approved) {
                id
                status
                updated_at
            }
        }
        GQL;

        $response = Http::withToken($token)->post('http://localhost:8087/graphql', [
            'query' => $mutationQuery
        ]);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Failed to approve reservation.');
        }

        $data = $response->json();
        if (isset($data['data']['updateReservationStatus'])) {
            return redirect()->back()->with('success', 'Reservation approved successfully!');
        }

        return redirect()->back()->with('error', 'Something went wrong.');
    }

}
