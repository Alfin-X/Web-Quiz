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
        // Get categories with quiz counts and statistics
        $categories = Category::withCount(['quizzes' => function($query) {
            $query->where('is_active', true);
        }])
        ->with(['quizzes' => function($query) {
            $query->where('is_active', true)
                  ->withCount(['attempts' => function($q) {
                      $q->whereNotNull('completed_at');
                  }]);
        }])
        ->get()
        ->map(function($category) {
            $totalAttempts = $category->quizzes->sum('attempts_count');
            $avgDifficulty = $category->quizzes->avg('time_limit') ?? 30;

            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'quiz_count' => $category->quizzes_count,
                'total_attempts' => $totalAttempts,
                'difficulty_level' => $this->getDifficultyLevel($avgDifficulty),
                'icon' => $this->getCategoryIcon($category->name),
                'color' => $this->getCategoryColor($category->name),
            ];
        });

        // User statistics
        $userStats = [
            'total_attempts' => QuizAttempt::where('user_id', auth()->id())->count(),
            'completed_quizzes' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->distinct('quiz_id')
                ->count(),
            'average_score' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->avg('score') ?? 0,
            'best_score' => QuizAttempt::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->max('score') ?? 0,
        ];

        // Recent activity
        $recentAttempts = QuizAttempt::with(['quiz.category'])
            ->where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('categories', 'userStats', 'recentAttempts'));
    }

    public function categoryQuizzes(Category $category, Request $request)
    {
        $query = Quiz::with(['category', 'creator'])
            ->withCount('questions')
            ->where('category_id', $category->id)
            ->where('is_active', true);

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $quizzes = $query->latest()->paginate(12);

        // Get user's completed quiz attempts
        $userCompletedQuizzes = QuizAttempt::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->pluck('quiz_id')
            ->toArray();

        return view('user.category-quizzes', compact('category', 'quizzes', 'userCompletedQuizzes'));
    }

    private function getDifficultyLevel($timeLimit)
    {
        if ($timeLimit <= 15) return 'Easy';
        if ($timeLimit <= 30) return 'Medium';
        if ($timeLimit <= 60) return 'Hard';
        return 'Expert';
    }

    private function getCategoryIcon($categoryName)
    {
        $icons = [
            'Matematika' => 'calculator',
            'Bahasa Indonesia' => 'book',
            'IPA' => 'flask',
            'IPS' => 'globe',
            'Sejarah' => 'clock-history',
            'Geografi' => 'geo-alt',
            'Fisika' => 'lightning',
            'Kimia' => 'droplet',
            'Biologi' => 'tree',
            'Bahasa Inggris' => 'translate',
        ];

        return $icons[$categoryName] ?? 'journal-text';
    }

    private function getCategoryColor($categoryName)
    {
        $colors = [
            'Matematika' => 'primary',
            'Bahasa Indonesia' => 'success',
            'IPA' => 'info',
            'IPS' => 'warning',
            'Sejarah' => 'secondary',
            'Geografi' => 'success',
            'Fisika' => 'primary',
            'Kimia' => 'danger',
            'Biologi' => 'success',
            'Bahasa Inggris' => 'info',
        ];

        return $colors[$categoryName] ?? 'primary';
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

    public function leaderboard(Request $request)
    {
        $query = QuizAttempt::with(['user', 'quiz.category'])
            ->whereNotNull('completed_at');

        // Filter by quiz if specified
        if ($request->quiz_id) {
            $query->where('quiz_id', $request->quiz_id);
        }

        // Filter by category if specified
        if ($request->category_id) {
            $query->whereHas('quiz', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filter by date range if specified
        if ($request->date_from) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        $leaderboard = $query->orderBy('score', 'desc')
            ->orderBy('completed_at', 'asc')
            ->paginate(50);

        $quizzes = Quiz::with('category')->where('is_active', true)->get();
        $categories = Category::all();

        // Get current user's position in leaderboard
        $userPosition = null;
        if ($request->quiz_id) {
            $userAttempt = QuizAttempt::where('user_id', auth()->id())
                ->where('quiz_id', $request->quiz_id)
                ->whereNotNull('completed_at')
                ->orderBy('score', 'desc')
                ->orderBy('completed_at', 'asc')
                ->first();

            if ($userAttempt) {
                $betterScores = QuizAttempt::where('quiz_id', $request->quiz_id)
                    ->whereNotNull('completed_at')
                    ->where(function($q) use ($userAttempt) {
                        $q->where('score', '>', $userAttempt->score)
                          ->orWhere(function($q2) use ($userAttempt) {
                              $q2->where('score', $userAttempt->score)
                                 ->where('completed_at', '<', $userAttempt->completed_at);
                          });
                    })
                    ->count();
                $userPosition = $betterScores + 1;
            }
        }

        return view('user.leaderboard', compact('leaderboard', 'quizzes', 'categories', 'userPosition'));
    }
}
