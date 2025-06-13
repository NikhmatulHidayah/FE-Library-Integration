<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReservationController extends Controller
{
    public function createReservation(Request $request, $id)
    {
        $request->validate([
            'reservation_date' => 'required|date',
            'expire_date' => 'required|date|after:reservation_date',
        ]);

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

        $reservationMutation = '
            mutation {
                createReservation(input: {
                    book_id: ' . $id . ',
                    reservation_date: "' . $request->reservation_date . '",
                    expire_date: "' . $request->expire_date . '"
                }) {
                    id
                    user_id
                    book_id
                    reservation_date
                    status
                    expire_date
                    created_at
                }
            }
        ';

        $reservationResponse = Http::withToken($token)->post('http://localhost:8087/graphql', [
            'query' => $reservationMutation,
        ]);

        $reservationResult = $reservationResponse->json();

        if (isset($reservationResult['errors'])) {
            return back()->withErrors('Failed to submit reservation request.');
        }


        return redirect('/')->with('success', 'Reservation submitted successfully.');
    }

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


        $response = Http::withToken($token)->post('http://localhost:8087/graphql', [
            'query' => '
                query {
                  getReservationByUserId(user_id: ' . $userId . ') {
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
            '
        ]);

        $reservations = $response->json()['data']['getReservationByUserId'];

        foreach ($reservations as &$reservation) {
            $reservation['reservation_date'] = Carbon::parse($reservation['reservation_date'])->format('d-m-Y');
            $reservation['expire_date'] = Carbon::parse($reservation['expire_date'])->format('d-m-Y');
            
            $bookResponse = Http::post('http://localhost:8082/graphql', [
                'query' => '
                    query {
                      getBookById(id: "' . $reservation['book_id'] . '") {
                        title
                      }
                    }
                '
            ]);

            $bookData = $bookResponse->json()['data']['getBookById'];
            $reservation['book_title'] = $bookData ? $bookData['title'] : 'Unknown Title';
        }

        return view('reservation', ['reservations' => $reservations, 'name'=> $name]);
    }
}
