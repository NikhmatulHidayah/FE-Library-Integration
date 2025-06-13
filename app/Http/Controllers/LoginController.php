<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $client = new Client();
    
        $graphqlQuery = [
            'query' => '
                mutation {
                    login(
                        email: "' . $request->email . '", 
                        password: "' . $request->password . '"
                    ) {
                        access_token
                        token_type
                        expires_in
                        user {
                            id
                            name
                            email
                            role
                        }
                    }
                }
            ',
        ];
    
        try {
            $response = $client->post('http://localhost:8081/graphql', [
                'json' => $graphqlQuery
            ]);
        
            $data = json_decode($response->getBody(), true);
        
            if (isset($data['data']['login'])) {
                $loginData = $data['data']['login'];
                $user = $loginData['user'];
            
                session(['access_token' => $loginData['access_token']]);
                session(['user' => $user]);
            
                if ($user['role'] === 'admin') {
                    return redirect('/admin');
                } else {
                    return redirect('/');
                }
            } else {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process request'], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->session()->forget('access_token');

        return redirect('/login');
    }
}
