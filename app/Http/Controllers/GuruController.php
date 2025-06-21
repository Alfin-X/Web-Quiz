<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_quizzes' => Quiz::where('created_by', auth()->id())->count(),
            'total_categories' => Category::count(),
            'total_students' => User::where('role', 'user')->count(),
            'total_attempts' => QuizAttempt::whereHas('quiz', function($query) {
                $query->where('created_by', auth()->id());
            })->whereNotNull('completed_at')->count(),
            'recent_attempts' => QuizAttempt::with(['user', 'quiz'])
                ->whereHas('quiz', function($query) {
                    $query->where('created_by', auth()->id());
                })
                ->whereNotNull('completed_at')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('guru.dashboard', compact('stats'));
    }

    public function quizzes()
    {
        $quizzes = Quiz::with(['category', 'creator'])
            ->withCount(['questions', 'attempts' => function($query) {
                $query->whereNotNull('completed_at');
            }])
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(10);

        return view('guru.quizzes', compact('quizzes'));
    }

    public function createQuiz()
    {
        $categories = Category::all();
        return view('guru.quiz-form', compact('categories'));
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

        return redirect()->route('guru.questions', $quiz)
            ->with('success', 'Quiz created successfully. Now add questions.');
    }

    public function editQuiz(Quiz $quiz)
    {
        // Ensure guru can only edit their own quizzes
        if ($quiz->created_by !== auth()->id()) {
            abort(403, 'You can only edit your own quizzes.');
        }

        $categories = Category::all();
        return view('guru.quiz-form', compact('quiz', 'categories'));
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        // Ensure guru can only update their own quizzes
        if ($quiz->created_by !== auth()->id()) {
            abort(403, 'You can only update your own quizzes.');
        }

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

        return redirect()->route('guru.quizzes')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        // Ensure guru can only delete their own quizzes
        if ($quiz->created_by !== auth()->id()) {
            abort(403, 'You can only delete your own quizzes.');
        }

        $quiz->delete();

        return redirect()->route('guru.quizzes')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function questions(Quiz $quiz)
    {
        // Ensure guru can only view questions of their own quizzes
        if ($quiz->created_by !== auth()->id()) {
            abort(403, 'You can only view questions of your own quizzes.');
        }

        $quiz->load(['questions.options', 'category']);
        return view('guru.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        // Ensure guru can only add questions to their own quizzes
        if ($quiz->created_by !== auth()->id()) {
            abort(403, 'You can only add questions to your own quizzes.');
        }

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

        return redirect()->route('guru.questions', $quiz)
            ->with('success', 'Question added successfully.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        // Ensure guru can only update questions of their own quizzes
        if ($question->quiz->created_by !== auth()->id()) {
            abort(403, 'You can only update questions of your own quizzes.');
        }

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

        return redirect()->route('guru.questions', $question->quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Question $question)
    {
        // Ensure guru can only delete questions of their own quizzes
        if ($question->quiz->created_by !== auth()->id()) {
            abort(403, 'You can only delete questions of your own quizzes.');
        }

        $quiz = $question->quiz;

        // Delete associated image if exists
        if ($question->image_path && \Storage::disk('public')->exists($question->image_path)) {
            \Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('guru.questions', $quiz)
            ->with('success', 'Question deleted successfully.');
    }

    public function statistics()
    {
        $stats = [
            'total_quizzes' => Quiz::where('created_by', auth()->id())->count(),
            'total_questions' => Question::whereHas('quiz', function($query) {
                $query->where('created_by', auth()->id());
            })->count(),
            'total_attempts' => QuizAttempt::whereHas('quiz', function($query) {
                $query->where('created_by', auth()->id());
            })->whereNotNull('completed_at')->count(),
            'total_students' => User::where('role', 'user')->count(),
            'popular_quizzes' => Quiz::where('created_by', auth()->id())
                ->withCount(['attempts' => function($query) {
                    $query->whereNotNull('completed_at');
                }])
                ->orderBy('attempts_count', 'desc')
                ->take(10)
                ->get(),
            'recent_attempts' => QuizAttempt::with(['user', 'quiz'])
                ->whereHas('quiz', function($query) {
                    $query->where('created_by', auth()->id());
                })
                ->whereNotNull('completed_at')
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('guru.statistics', compact('stats'));
    }

    public function leaderboard(Request $request)
    {
        $query = QuizAttempt::with(['user', 'quiz.category'])
            ->whereNotNull('completed_at')
            ->whereHas('quiz', function($q) {
                $q->where('created_by', auth()->id());
            });

        // Filter by quiz if specified (only guru's quizzes)
        if ($request->quiz_id) {
            $quiz = Quiz::where('id', $request->quiz_id)
                ->where('created_by', auth()->id())
                ->first();
            if ($quiz) {
                $query->where('quiz_id', $request->quiz_id);
            }
        }

        // Filter by category if specified
        if ($request->category_id) {
            $query->whereHas('quiz', function($q) use ($request) {
                $q->where('category_id', $request->category_id)
                  ->where('created_by', auth()->id());
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

        $quizzes = Quiz::with('category')
            ->where('created_by', auth()->id())
            ->where('is_active', true)
            ->get();
        $categories = Category::all();

        return view('guru.leaderboard', compact('leaderboard', 'quizzes', 'categories'));
    }
}
