<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Facades\JWTAuth;
use Firebase\JWT\JWT;

class CourseController extends Controller
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

        $url = 'http://localhost:5002/graphql';

        $query = '{
            allCourses {
                id
                title
                description
            }
        }';

        $response = $client->post($url, [
            'json' => [
                'query' => $query
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        //dd($data);
        return view('course', [
            'courses' => $data['data']['allCourses'] ?? [],
            'name' => $name
        ]);
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
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $name = $payload->get('name');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        // Create the GraphQL query to fetch the course by ID
        $client = new Client();
        $url = 'http://localhost:5002/graphql';

        $query = "
        {
            course(id: $id) {
                id
                title
                description
                content
            }
        }";

        // Make the request to the GraphQL API
        $response = $client->post($url, [
            'json' => [
                'query' => $query
            ]
        ]);

        // Parse the response
        $data = json_decode($response->getBody()->getContents(), true);
        $course = $data['data']['course'];

        // Return the view with the course data
        return view('showCourse', compact('course', 'name'));
    }
}
