<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $client = new Client();

        $mutation = <<<GQL
            mutation {
                register(
                    name: "{$request->name}",
                    email: "{$request->email}",
                    password: "{$request->password}",
                    role: "student"
                ) {
                    id
                    name
                    email
                    role
                }
            }
        GQL;

        try {
            $response = $client->post('http://localhost:8081/graphql', [
                'json' => ['query' => $mutation],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['data']['register'])) {
                return redirect('/login')->with('status', 'Registrasi berhasil! Silakan login.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Registrasi gagal. Silakan coba lagi.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

}
