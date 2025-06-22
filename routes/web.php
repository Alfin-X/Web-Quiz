<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
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
    Route::get('/category/{category}/quizzes', [UserDashboardController::class, 'categoryQuizzes'])->name('user.category.quizzes');
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
    Route::get('/quiz/{quiz}/take/{attempt}', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');
    Route::get('/my-results', [UserDashboardController::class, 'results'])->name('user.results');
    Route::get('/leaderboard', [UserDashboardController::class, 'leaderboard'])->name('user.leaderboard');

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

        // Leaderboard
        Route::get('/leaderboard', [AdminController::class, 'leaderboard'])->name('leaderboard');

        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

        // Export Data
        Route::get('/export-data', [AdminController::class, 'exportData'])->name('export.data');
        Route::post('/export-custom', [AdminController::class, 'exportCustomData'])->name('export.custom');
        Route::get('/export/{quiz}', [AdminController::class, 'exportResults'])->name('export');

        // User Management Routes
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/bulk-delete', [AdminController::class, 'bulkDeleteUsers'])->name('users.bulk-delete');
    });

    // Guru Routes
    Route::middleware(['guru'])->prefix('guru')->name('guru.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');

        // Quizzes
        Route::get('/quizzes', [GuruController::class, 'quizzes'])->name('quizzes');
        Route::get('/quizzes/create', [GuruController::class, 'createQuiz'])->name('quizzes.create');
        Route::post('/quizzes', [GuruController::class, 'storeQuiz'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}/edit', [GuruController::class, 'editQuiz'])->name('quizzes.edit');
        Route::put('/quizzes/{quiz}', [GuruController::class, 'updateQuiz'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [GuruController::class, 'destroyQuiz'])->name('quizzes.destroy');

        // Questions
        Route::get('/quizzes/{quiz}/questions', [GuruController::class, 'questions'])->name('questions');
        Route::post('/quizzes/{quiz}/questions', [GuruController::class, 'storeQuestion'])->name('questions.store');
        Route::put('/questions/{question}', [GuruController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [GuruController::class, 'destroyQuestion'])->name('questions.destroy');

        // Statistics & Leaderboard
        Route::get('/statistics', [GuruController::class, 'statistics'])->name('statistics');
        Route::get('/leaderboard', [GuruController::class, 'leaderboard'])->name('leaderboard');
    });

    // API Routes for AJAX
    Route::get('/api/questions/{question}', function(\App\Models\Question $question) {
        // Ensure guru can only access their own questions
        if ($question->quiz->created_by !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return response()->json([
            'id' => $question->id,
            'question_text' => $question->question_text,
            'image_path' => $question->image_path,
            'options' => $question->options->map(function($option, $index) {
                return [
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct
                ];
            })
        ]);
    })->middleware('auth');
});

// API Routes for AJAX
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/quiz/{quiz}/question/{question}', [QuizController::class, 'getQuestion']);
    Route::post('/quiz/{quiz}/answer', [QuizController::class, 'saveAnswer'])->name('quiz.saveAnswer');
    Route::get('/quiz/{quiz}/time-remaining/{attempt}', [QuizController::class, 'getTimeRemaining']);
});
