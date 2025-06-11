<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Category;
use App\Models\QuizAttempt;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['category', 'creator'])
            ->withCount(['questions', 'attempts'])
            ->where('is_active', true);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        $quizzes = $query->latest()->paginate(12);
        $categories = Category::all();

        // User statistics
        $userStats = [
            'total_attempts' => QuizAttempt::where('user_id', auth()->id())->count(),
            'completed_quizzes' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->count(),
            'average_score' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->avg('score') ?? 0,
            'recent_attempts' => QuizAttempt::with('quiz')
                ->where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('user.dashboard', compact('quizzes', 'categories', 'userStats'));
    }

    public function results()
    {
        $attempts = QuizAttempt::with(['quiz.category'])
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_completed' => $attempts->total(),
            'average_score' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->avg('score') ?? 0,
            'best_score' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->max('score') ?? 0,
            'total_time_spent' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->get()
                ->sum('duration') ?? 0,
        ];

        return view('user.results', compact('attempts', 'stats'));
    }
}
