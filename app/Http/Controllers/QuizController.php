<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        if (!$quiz->is_active) {
            abort(404, 'Quiz not found or inactive.');
        }

        $quiz->load(['category', 'creator', 'questions.options']);

        // Check if user has already completed this quiz
        $existingAttempt = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->first();

        return view('quiz.show', compact('quiz', 'existingAttempt'));
    }

    public function start(Request $request, Quiz $quiz)
    {
        if (!$quiz->is_active) {
            return response()->json(['error' => 'Quiz is not active'], 400);
        }

        // Check if user already has an active attempt
        $activeAttempt = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($activeAttempt) {
            return response()->json([
                'success' => true,
                'attempt_id' => $activeAttempt->id,
                'redirect' => route('quiz.take', ['quiz' => $quiz->id, 'attempt' => $activeAttempt->id])
            ]);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'total_questions' => $quiz->questions()->count(),
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'redirect' => route('quiz.take', ['quiz' => $quiz->id, 'attempt' => $attempt->id])
        ]);
    }

    public function take(Quiz $quiz, QuizAttempt $attempt)
    {
        // Verify attempt belongs to current user
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if attempt is already completed
        if ($attempt->completed_at) {
            return redirect()->route('quiz.result', ['quiz' => $quiz, 'attempt' => $attempt]);
        }

        // Check if time limit exceeded
        $timeLimit = $quiz->time_limit * 60; // Convert to seconds
        $elapsed = $attempt->started_at->diffInSeconds(now());

        if ($elapsed >= $timeLimit) {
            $this->completeQuiz($attempt);
            return redirect()->route('quiz.result', ['quiz' => $quiz, 'attempt' => $attempt]);
        }

        $questions = $quiz->questions()->with('options')->get();
        $userAnswers = UserAnswer::where('attempt_id', $attempt->id)
            ->pluck('selected_option_id', 'question_id')
            ->toArray();

        $timeRemaining = $timeLimit - $elapsed;

        return view('quiz.take', compact('quiz', 'attempt', 'questions', 'userAnswers', 'timeRemaining'));
    }

    public function saveAnswer(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'required|exists:question_options,id',
        ]);

        $attempt = QuizAttempt::findOrFail($request->attempt_id);

        // Verify attempt belongs to current user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if attempt is still active
        if ($attempt->completed_at) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        $question = Question::findOrFail($request->question_id);
        $selectedOption = $question->options()->findOrFail($request->option_id);

        // Save or update user answer
        UserAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'selected_option_id' => $selectedOption->id,
                'is_correct' => $selectedOption->is_correct,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
        ]);

        $attempt = QuizAttempt::findOrFail($request->attempt_id);

        // Verify attempt belongs to current user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->completeQuiz($attempt);

        return response()->json([
            'success' => true,
            'redirect' => route('quiz.result', ['quiz' => $quiz, 'attempt' => $attempt])
        ]);
    }

    private function completeQuiz(QuizAttempt $attempt)
    {
        if ($attempt->completed_at) {
            return; // Already completed
        }

        $correctAnswers = UserAnswer::where('attempt_id', $attempt->id)
            ->where('is_correct', true)
            ->count();

        $score = $attempt->total_questions > 0
            ? round(($correctAnswers / $attempt->total_questions) * 100, 2)
            : 0;

        $attempt->update([
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'completed_at' => now(),
        ]);
    }

    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        // Verify attempt belongs to current user
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure quiz is completed
        if (!$attempt->completed_at) {
            return redirect()->route('quiz.take', ['quiz' => $quiz, 'attempt' => $attempt]);
        }

        $attempt->load(['userAnswers.question.options', 'userAnswers.selectedOption']);

        // Get leaderboard
        $leaderboard = QuizAttempt::with('user')
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->orderBy('score', 'desc')
            ->orderBy('completed_at', 'asc')
            ->take(10)
            ->get();

        return view('quiz.result', compact('quiz', 'attempt', 'leaderboard'));
    }

    // API Methods for AJAX
    public function getQuestion(Quiz $quiz, Question $question)
    {
        $question->load('options');
        return response()->json($question);
    }

    public function getTimeRemaining(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $timeLimit = $quiz->time_limit * 60; // Convert to seconds
        $elapsed = $attempt->started_at->diffInSeconds(now());
        $remaining = max(0, $timeLimit - $elapsed);

        return response()->json(['time_remaining' => $remaining]);
    }
}
