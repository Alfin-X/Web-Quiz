<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // User Dashboard Routes
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/quiz/{quiz}/take/{attempt}', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');
    Route::get('/my-results', [UserDashboardController::class, 'results'])->name('user.results');

    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Categories
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

        // Quizzes
        Route::get('/quizzes', [AdminController::class, 'quizzes'])->name('quizzes');
        Route::get('/quizzes/create', [AdminController::class, 'createQuiz'])->name('quizzes.create');
        Route::post('/quizzes', [AdminController::class, 'storeQuiz'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}/edit', [AdminController::class, 'editQuiz'])->name('quizzes.edit');
        Route::put('/quizzes/{quiz}', [AdminController::class, 'updateQuiz'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [AdminController::class, 'destroyQuiz'])->name('quizzes.destroy');

        // Questions
        Route::get('/quizzes/{quiz}/questions', [AdminController::class, 'questions'])->name('questions');
        Route::post('/quizzes/{quiz}/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');
        Route::put('/questions/{question}', [AdminController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [AdminController::class, 'destroyQuestion'])->name('questions.destroy');

        // Statistics
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
        Route::get('/export/{quiz}', [AdminController::class, 'exportResults'])->name('export');
    });
});

// API Routes for AJAX
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/quiz/{quiz}/question/{question}', [QuizController::class, 'getQuestion']);
    Route::post('/quiz/{quiz}/answer', [QuizController::class, 'saveAnswer'])->name('quiz.saveAnswer');
    Route::get('/quiz/{quiz}/time-remaining/{attempt}', [QuizController::class, 'getTimeRemaining']);
});
