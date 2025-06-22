<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_quizzes' => Quiz::count(),
            'total_categories' => Category::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_attempts' => QuizAttempt::count(),
            'recent_attempts' => QuizAttempt::with(['user', 'quiz'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function categories()
    {
        $categories = Category::withCount('quizzes')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories')
            ->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->quizzes()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'Cannot delete category with existing quizzes.');
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Category deleted successfully.');
    }

    public function quizzes()
    {
        $quizzes = Quiz::with(['category', 'creator'])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->get();

        return view('admin.quizzes', compact('quizzes'));
    }

    public function createQuiz()
    {
        $categories = Category::all();
        return view('admin.quiz-form', compact('categories'));
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'time_limit' => 'required|integer|min:1|max:180',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'time_limit' => $request->time_limit,
            'created_by' => auth()->id(),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.questions', $quiz)
            ->with('success', 'Quiz created successfully. Now add questions.');
    }

    public function editQuiz(Quiz $quiz)
    {
        $categories = Category::all();
        return view('admin.quiz-form', compact('quiz', 'categories'));
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'time_limit' => 'required|integer|min:1|max:180',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'time_limit' => $request->time_limit,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.quizzes')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function questions(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->get();
        return view('admin.questions', compact('quiz', 'questions'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        DB::transaction(function () use ($request, $quiz) {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('question-images', 'public');
            }

            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'order' => $quiz->questions()->count() + 1,
            ]);

            foreach ($request->options as $index => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => $index == $request->correct_option,
                ]);
            }
        });

        return redirect()->route('admin.questions', $quiz)
            ->with('success', 'Question added successfully.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        DB::transaction(function () use ($request, $question) {
            // Handle image upload
            $imagePath = $question->image_path; // Keep existing image if no new upload
            if ($request->hasFile('question_image')) {
                // Delete old image if exists
                if ($question->image_path && \Storage::disk('public')->exists($question->image_path)) {
                    \Storage::disk('public')->delete($question->image_path);
                }
                $imagePath = $request->file('question_image')->store('question-images', 'public');
            }

            $question->update([
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
            ]);

            // Delete existing options
            $question->options()->delete();

            // Create new options
            foreach ($request->options as $index => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => $index == $request->correct_option,
                ]);
            }
        });

        return redirect()->route('admin.questions', $question->quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Question $question)
    {
        $quiz = $question->quiz;

        // Delete associated image if exists
        if ($question->image_path && \Storage::disk('public')->exists($question->image_path)) {
            \Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('admin.questions', $quiz)
            ->with('success', 'Question deleted successfully.');
    }

    public function statistics()
    {
        $stats = [
            'total_quizzes' => Quiz::count(),
            'total_questions' => Question::count(),
            'total_attempts' => QuizAttempt::count(),
            'total_users' => User::where('role', 'user')->count(),
            'popular_quizzes' => Quiz::withCount('attempts')
                ->orderBy('attempts_count', 'desc')
                ->take(10)
                ->get(),
            'recent_attempts' => QuizAttempt::with(['user', 'quiz'])
                ->latest()
                ->take(10)
                ->get(),
            'category_stats' => Category::withCount(['quizzes'])
                ->with(['quizzes' => function($query) {
                    $query->withCount('attempts');
                }])
                ->get()
                ->map(function($category) {
                    return [
                        'name' => $category->name,
                        'quizzes_count' => $category->quizzes_count,
                        'total_attempts' => $category->quizzes->sum('attempts_count'),
                    ];
                }),
        ];

        return view('admin.statistics', compact('stats'));
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

        return view('admin.leaderboard', compact('leaderboard', 'quizzes', 'categories'));
    }

    public function analytics(Request $request)
    {
        // Basic stats
        $totalQuizzes = Quiz::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalAttempts = QuizAttempt::whereNotNull('completed_at')->count();
        $totalQuestions = Question::count();



        // Question difficulty analysis
        $questionDifficulty = DB::table('questions')
            ->leftJoin('user_answers', 'questions.id', '=', 'user_answers.question_id')
            ->leftJoin('question_options', function($join) {
                $join->on('user_answers.selected_option_id', '=', 'question_options.id')
                     ->where('question_options.is_correct', true);
            })
            ->select('questions.id', 'questions.question_text')
            ->selectRaw('
                COUNT(user_answers.id) as total_answers,
                COUNT(question_options.id) as correct_answers,
                CASE
                    WHEN COUNT(user_answers.id) > 0
                    THEN ROUND((COUNT(question_options.id) * 100.0 / COUNT(user_answers.id)), 2)
                    ELSE 0
                END as success_rate
            ')
            ->groupBy('questions.id', 'questions.question_text')
            ->having('total_answers', '>', 0)
            ->orderBy('success_rate', 'asc')
            ->get();



        // Category performance
        $categoryPerformance = Category::withCount('quizzes')
            ->with(['quizzes' => function($query) {
                $query->withCount('attempts')
                      ->with(['attempts' => function($q) {
                          $q->whereNotNull('completed_at');
                      }]);
            }])
            ->get()
            ->map(function($category) {
                $totalAttempts = $category->quizzes->sum('attempts_count');
                $avgScore = 0;

                if ($totalAttempts > 0) {
                    $allAttempts = $category->quizzes->flatMap->attempts;
                    $avgScore = $allAttempts->avg('score') ?? 0;
                }

                return [
                    'name' => $category->name,
                    'quizzes_count' => $category->quizzes_count,
                    'total_attempts' => $totalAttempts,
                    'avg_score' => round($avgScore, 2),
                ];
            });

        // Top performers
        $topPerformers = User::where('role', 'user')
            ->withCount(['quizAttempts as completed_quizzes' => function($query) {
                $query->whereNotNull('completed_at');
            }])
            ->with(['quizAttempts' => function($query) {
                $query->whereNotNull('completed_at');
            }])
            ->get()
            ->map(function($user) {
                $avgScore = $user->quizAttempts->avg('score') ?? 0;
                return [
                    'user' => $user,
                    'completed_quizzes' => $user->completed_quizzes,
                    'avg_score' => round($avgScore, 2),
                ];
            })
            ->sortByDesc('avg_score')
            ->take(10);

        return view('admin.analytics', compact(
            'totalQuizzes', 'totalUsers', 'totalAttempts', 'totalQuestions',
            'questionDifficulty', 'categoryPerformance', 'topPerformers'
        ));
    }

    public function exportData()
    {
        $quizzes = Quiz::with('category')->where('is_active', true)->get();
        $categories = Category::all();

        return view('admin.export', compact('quizzes', 'categories'));
    }

    public function exportResults(Quiz $quiz)
    {
        $attempts = QuizAttempt::with(['user', 'userAnswers.question', 'userAnswers.selectedOption'])
            ->where('quiz_id', $quiz->id)
            ->where('completed_at', '!=', null)
            ->get();

        $filename = 'quiz_results_' . $quiz->id . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'User Name',
                'Email',
                'Score',
                'Total Questions',
                'Correct Answers',
                'Completion Time',
                'Started At',
                'Completed At',
                'Duration (minutes)'
            ]);

            foreach ($attempts as $attempt) {
                $duration = $attempt->started_at && $attempt->completed_at
                    ? $attempt->started_at->diffInMinutes($attempt->completed_at)
                    : 0;

                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->score,
                    $attempt->total_questions,
                    $attempt->correct_answers,
                    $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : '',
                    $attempt->started_at->format('Y-m-d H:i:s'),
                    $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : '',
                    $duration
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCustomData(Request $request)
    {
        $request->validate([
            'export_type' => 'required|in:all_results,quiz_specific,category_specific,date_range',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'category_id' => 'nullable|exists:categories,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = QuizAttempt::with(['user', 'quiz.category'])
            ->whereNotNull('completed_at');

        // Apply filters based on export type
        switch ($request->export_type) {
            case 'quiz_specific':
                if ($request->quiz_id) {
                    $query->where('quiz_id', $request->quiz_id);
                }
                break;
            case 'category_specific':
                if ($request->category_id) {
                    $query->whereHas('quiz', function($q) use ($request) {
                        $q->where('category_id', $request->category_id);
                    });
                }
                break;
            case 'date_range':
                if ($request->date_from) {
                    $query->whereDate('completed_at', '>=', $request->date_from);
                }
                if ($request->date_to) {
                    $query->whereDate('completed_at', '<=', $request->date_to);
                }
                break;
        }

        $attempts = $query->orderBy('completed_at', 'desc')->get();

        $filename = 'quiz_export_' . $request->export_type . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'User Name',
                'Email',
                'Quiz Title',
                'Category',
                'Score (%)',
                'Total Questions',
                'Correct Answers',
                'Started At',
                'Completed At',
                'Duration (minutes)',
                'Performance Level'
            ]);

            foreach ($attempts as $attempt) {
                $duration = $attempt->started_at && $attempt->completed_at
                    ? $attempt->started_at->diffInMinutes($attempt->completed_at)
                    : 0;

                $performanceLevel = 'Needs Improvement';
                if ($attempt->score >= 90) $performanceLevel = 'Excellent';
                elseif ($attempt->score >= 80) $performanceLevel = 'Very Good';
                elseif ($attempt->score >= 70) $performanceLevel = 'Good';
                elseif ($attempt->score >= 60) $performanceLevel = 'Fair';

                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->quiz->title,
                    $attempt->quiz->category->name,
                    $attempt->score,
                    $attempt->total_questions,
                    $attempt->correct_answers,
                    $attempt->started_at->format('Y-m-d H:i:s'),
                    $attempt->completed_at ? $attempt->completed_at->format('Y-m-d H:i:s') : '',
                    $duration,
                    $performanceLevel
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // User Management Methods
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15);

        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_gurus' => User::where('role', 'guru')->count(),
            'total_students' => User::where('role', 'user')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,guru,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(), // Auto verify admin-created accounts
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User account created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,guru,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Only update password if provided
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')
            ->with('success', 'User account updated successfully!');
    }

    public function destroyUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account!');
        }

        // Prevent deleting the last admin
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')
                ->with('error', 'Cannot delete the last admin account!');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User account deleted successfully!');
    }

    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;

        // Remove current admin from deletion list
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });

        // Check if trying to delete all admins
        $adminCount = User::where('role', 'admin')->count();
        $adminsToDelete = User::whereIn('id', $userIds)->where('role', 'admin')->count();

        if ($adminCount - $adminsToDelete < 1) {
            return redirect()->route('admin.users')
                ->with('error', 'Cannot delete all admin accounts! At least one admin must remain.');
        }

        User::whereIn('id', $userIds)->delete();

        return redirect()->route('admin.users')
            ->with('success', count($userIds) . ' user accounts deleted successfully!');
    }
}
