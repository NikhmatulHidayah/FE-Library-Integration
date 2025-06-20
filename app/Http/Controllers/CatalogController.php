<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use GraphQL;
use Exception;

class CatalogController extends Controller
{
    public function index(Request $request)
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

        if ($role !== 'student') {
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
        return view('catalog', ['books' => $books], ['name' => $name]);
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

        if ($role !== 'student') {
            return redirect('/logout');
        }   

        try {
            $bookQuery = '
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

            $reviewQuery = '
                query {
                  getReviewByBookId(book_id: "' . $id . '") {
                    id
                    book_id
                    user_id
                    rating
                    comment
                    created_at
                    updated_at
                  }
                }
            ';  

            $bookResponse = Http::post('http://localhost:8082/graphql', [
                'query' => $bookQuery
            ]); 

            $bookData = $bookResponse->json();  

            if (isset($bookData['errors'])) {
                return abort(404, 'Book not found');
            }   

            $book = $bookData['data']['getBookById'];   

            $reviewResponse = Http::post('http://localhost:8089/graphql', [
                'query' => $reviewQuery
            ]); 

            $reviewData = $reviewResponse->json();  

            if (isset($reviewData['errors'])) {
                $reviews = [];
            } else {
                $reviews = $reviewData['data']['getReviewByBookId'];
            }   

            return view('book_detail', [
                'name' => $name,
                'book' => $book,
                'reviews' => $reviews
            ]); 

        } catch (Exception $e) {
            return abort(500, 'Error fetching data: ' . $e->getMessage());
        }
    }

}
