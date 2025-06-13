<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Firebase\JWT\JWT;


class HomeController extends Controller
{
    public function index()
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
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        $client = new Client();

        $query = <<<GQL
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

        $response = $client->post('http://localhost:8082/graphql', [
            'json' => ['query' => $query]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $books = array_slice($data['data']['books'], 0, 10);

        return view('home', ['books' => $books, 'name' => $name]);
    }
}
