<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoanController extends Controller
{
    public function showLoanForm($id)
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

        $query = '
            query {
              getBookById(id: "' . $id . '") {
                id
                title
                images {
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
            return abort(404, 'Book not found');
        }

        $book = $data['data']['getBookById'];

        return view('loan', compact('book', 'name'));
    }

    public function submitLoan(Request $request, $id)
    {
        $request->validate([
            'loan_date' => 'required|date',
            'return_date' => 'required|date|after:loan_date',
        ]);

        $token = session('access_token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            JWTAuth::setToken($token);
            $payload = JWTAuth::getPayload();
            $userId = $payload->get('id');
            $role = $payload->get('role');
        } catch (\Exception $e) {
            return redirect('/login');
        }

        if ($role !== 'student') {
            return redirect('/logout');
        }

        $loanMutation = '
            mutation {
                createLoan(input: {
                    book_id: ' . $id . ',
                    loan_date: "' . $request->loan_date . '",
                    return_date: "' . $request->return_date . '"
                }) {
                    id
                    book_id
                }
            }
        ';

        $loanResponse = Http::withToken($token)->post('http://localhost:8085/graphql', [
            'query' => $loanMutation,
        ]);

        $loanResult = $loanResponse->json();

        if (isset($loanResult['errors'])) {
            return back()->withErrors('Failed to submit loan request.');
        }

        return redirect('/')->with('success', 'Loan submitted successfully.');
    }

    public function getLoansByUser(Request $request)
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

        $responseLoans = Http::post('http://localhost:8085/graphql', [
            'query' => '
                query {
                    loanByUser(user_id: ' . $userId . ') {
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

        if ($responseLoans->successful()) {
            $loans = $responseLoans->json()['data']['loanByUser'];  

            foreach ($loans as &$loan) {
                $responseBook = Http::post('http://localhost:8082/graphql', [
                    'query' => '
                        query {
                            getBookById(id: "' . $loan['book_id'] . '") {
                                title
                            }
                        }
                    '
                ]); 

                if ($responseBook->successful()) {
                    $book = $responseBook->json()['data']['getBookById'];
                    $loan['title'] = $book['title'];
                }
            }   

            $activeLoans = array_filter($loans, fn($loan) => $loan['status'] !== 'returned');
            $returnedLoans = array_filter($loans, fn($loan) => $loan['status'] === 'returned'); 

            return view('loan-main', compact('activeLoans', 'returnedLoans', 'name'));
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function returnBook($loanId, Request $request)
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

        //dd($loanId);

        $response = Http::withToken($token)->post('http://localhost:8085/graphql', [
            'query' => '
                mutation {
                    returnBook(id: ' . $loanId . ') {
                        id
                        user_id
                        book_id
                        loan_date
                        status
                        return_date
                        created_at
                        updated_at
                    }
                }
            '
        ]);

        //dd($response->json());

        if ($response->successful()) {
            session()->flash('success', 'Book returned successfully!');
        } else {
            session()->flash('error', 'Failed to return the book. Please try again.');
        }

        return redirect()->route('loan.getLoansByUser');
    }

}
