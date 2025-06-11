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
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        DB::transaction(function () use ($request, $quiz) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $request->question_text,
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
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0|max:3',
        ]);

        DB::transaction(function () use ($request, $question) {
            $question->update([
                'question_text' => $request->question_text,
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
                'Percentage',
                'Correct Answers',
                'Total Questions',
                'Started At',
                'Completed At',
                'Duration (minutes)'
            ]);

            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    $attempt->score,
                    $attempt->percentage . '%',
                    $attempt->correct_answers,
                    $attempt->total_questions,
                    $attempt->started_at->format('Y-m-d H:i:s'),
                    $attempt->completed_at->format('Y-m-d H:i:s'),
                    $attempt->duration
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
