<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReviewController extends Controller
{
public function submitReview(Request $request, $loanId)
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

    $rating = $request->input('rating');
    $comment = $request->input('comment');
    $bookId = $request->input('book_id');

    //dd($bookId);

    if ($rating && $rating >= 1 && $rating <= 5) {
        $response = Http::withToken($token)->post('http://localhost:8089/graphql', [
            'query' => '
                mutation {
                    createReview(input: {
                        book_id: ' . (int)$bookId . ',
                        rating: ' . (int)$rating . ',
                        comment: "' . addslashes($comment) . '"
                    }) {
                        id
                        book_id
                        user_id
                        rating
                        comment
                        created_at
                    }
                }
            '
        ]);

        //dd($response->json());

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Review submitted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to submit review.');
        }
    } else {
        return redirect()->back()->with('error', 'Invalid rating.');
    }
}


}
