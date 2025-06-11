@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-trophy"></i> My Quiz Results</h1>
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_completed'] }}</h4>
                    <p class="mb-0">Completed Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['average_score'], 1) }}%</h4>
                    <p class="mb-0">Average Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($stats['best_score'], 1) }}%</h4>
                    <p class="mb-0">Best Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_time_spent'] }}</h4>
                    <p class="mb-0">Total Time (min)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-list-check"></i> Quiz History</h5>
                </div>
                <div class="card-body">
                    @if($attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Category</th>
                                        <th>Score</th>
                                        <th>Correct Answers</th>
                                        <th>Time Taken</th>
                                        <th>Completed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $attempt)
                                        <tr>
                                            <td>
                                                <strong>{{ $attempt->quiz->title }}</strong>
                                                @if($attempt->quiz->description)
                                                    <br><small class="text-muted">{{ Str::limit($attempt->quiz->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $attempt->quiz->category->name }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }} me-2">
                                                        {{ $attempt->score }}%
                                                    </span>
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}" 
                                                             role="progressbar" style="width: {{ $attempt->score }}%" 
                                                             aria-valuenow="{{ $attempt->score }}" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $attempt->correct_answers }}</strong> / {{ $attempt->total_questions }}
                                                <br><small class="text-muted">{{ $attempt->percentage }}%</small>
                                            </td>
                                            <td>
                                                @if($attempt->duration)
                                                    {{ $attempt->duration }} min
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {{ $attempt->completed_at->format('M d, Y') }}
                                                <br><small class="text-muted">{{ $attempt->completed_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('quiz.result', ['quiz' => $attempt->quiz, 'attempt' => $attempt]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $attempts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-trophy fs-1 text-muted"></i>
                            <h4 class="text-muted">No quiz results found</h4>
                            <p class="text-muted">You haven't completed any quizzes yet. Start taking quizzes to see your results here.</p>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-play-circle"></i> Take a Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($attempts->count() > 0)
        <!-- Performance Chart -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Performance Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Score Distribution</h6>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Excellent (80-100%)</span>
                                        <span>{{ $attempts->where('score', '>=', 80)->count() }} quizzes</span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $attempts->count() > 0 ? ($attempts->where('score', '>=', 80)->count() / $attempts->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Good (60-79%)</span>
                                        <span>{{ $attempts->whereBetween('score', [60, 79])->count() }} quizzes</span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $attempts->count() > 0 ? ($attempts->whereBetween('score', [60, 79])->count() / $attempts->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Needs Improvement (0-59%)</span>
                                        <span>{{ $attempts->where('score', '<', 60)->count() }} quizzes</span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ $attempts->count() > 0 ? ($attempts->where('score', '<', 60)->count() / $attempts->count()) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Recent Performance Trend</h6>
                                @php
                                    $recentAttempts = $attempts->take(5);
                                    $trend = $recentAttempts->count() > 1 ? 
                                        ($recentAttempts->first()->score - $recentAttempts->last()->score) : 0;
                                @endphp
                                <div class="text-center">
                                    @if($trend > 0)
                                        <i class="bi bi-trending-up fs-1 text-success"></i>
                                        <p class="text-success">Improving! (+{{ number_format($trend, 1) }}%)</p>
                                    @elseif($trend < 0)
                                        <i class="bi bi-trending-down fs-1 text-danger"></i>
                                        <p class="text-danger">Declining ({{ number_format($trend, 1) }}%)</p>
                                    @else
                                        <i class="bi bi-dash fs-1 text-muted"></i>
                                        <p class="text-muted">Stable performance</p>
                                    @endif
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">Based on your last {{ $recentAttempts->count() }} quiz(zes)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
