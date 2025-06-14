<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CourseController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', [HomeController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/catalog/book/{id}', [CatalogController::class, 'show']);

Route::get('/logout', [LoginController::class, 'logout']);

Route::get('/admin', [AdminController::class, 'showAdminHome']);

Route::get('/admin/catalog', [AdminController::class, 'showAdminCatalog']);

Route::get('/admin/add/book/', [AdminController::class, 'showAdminAddBook']);
Route::post('/admin/add/book/post', [AdminController::class, 'store'])->name('admin.books.store');

Route::get('/admin/catalog/book/{id}', [AdminController::class, 'show'])->name('book.detail');

Route::get('/admin/loan', [AdminController::class, 'loan'])->name('admin.loan');

Route::get('/admin/reservation', [AdminController::class, 'reservation'])->name('admin.reservation');
Route::post('/admin/approve-reservation/{id}', [AdminController::class, 'approveReservation'])->name('admin.approveReservation');

Route::get('/admin/catalog/book/{id}/edit', [AdminController::class, 'edit'])->name('book.edit');
Route::post('/admin/catalog/book/{id}/update', [AdminController::class, 'update'])->name('book.update');
Route::delete('/admin/catalog/book/{id}/delete', [AdminController::class, 'destroy'])->name('book.delete');

Route::get('/loan', [LoanController::class, 'getLoansByUser'])->name('loan.getLoansByUser');

Route::get('/loan/book/{id}', [LoanController::class, 'showLoanForm'])->name('loan.form');
Route::post('/loan/book/{id}', [LoanController::class, 'submitLoan'])->name('loan.submit');
Route::put('/loan/return/{loanId}', [LoanController::class, 'returnBook'])->name('loan.return');

Route::post('/review/{loanId}', [ReviewController::class, 'submitReview'])->name('review.submit');

Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::post('/reservation/book/{id}', [ReservationController::class, 'createReservation']);

Route::get('/course/all', [CourseController::class, 'index'])->name('course.index');
Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');
