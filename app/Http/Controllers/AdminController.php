<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use GraphQL;
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
        $query = '
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
        ';

        $response = Http::post('http://localhost:8082/graphql', [
            'query' => $query,
        ]);

        $data = $response->json();

        if (isset($data['errors'])) {
            return abort(500, 'Failed to fetch books data.');
        }

        $books = $data['data']['books'];

        $totalStock = array_reduce($books, function ($carry, $book) {
            return $carry + $book['stock'];
        }, 0);
        //dd($totalStock);
        return view('admin.home', ['name' => 'Admin', 'totalStock' => $totalStock]);
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
}
