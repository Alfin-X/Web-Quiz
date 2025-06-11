@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-graph-up"></i> Statistics & Analytics</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_quizzes'] }}</h4>
                    <p class="mb-0">Total Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_questions'] }}</h4>
                    <p class="mb-0">Total Questions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_attempts'] }}</h4>
                    <p class="mb-0">Quiz Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $stats['total_users'] }}</h4>
                    <p class="mb-0">Registered Users</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Quizzes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-star"></i> Most Popular Quizzes</h5>
                </div>
                <div class="card-body">
                    @if($stats['popular_quizzes']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Category</th>
                                        <th>Attempts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['popular_quizzes'] as $quiz)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($quiz->title, 30) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $quiz->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $quiz->attempts_count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No quiz attempts yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Category Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-tags"></i> Category Statistics</h5>
                </div>
                <div class="card-body">
                    @if($stats['category_stats']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Quizzes</th>
                                        <th>Attempts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['category_stats'] as $category)
                                        <tr>
                                            <td><strong>{{ $category['name'] }}</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ $category['quizzes_count'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $category['total_attempts'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No categories found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> Recent Quiz Attempts</h5>
                </div>
                <div class="card-body">
                    @if($stats['recent_attempts']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Quiz</th>
                                        <th>Category</th>
                                        <th>Score</th>
                                        <th>Correct/Total</th>
                                        <th>Duration</th>
                                        <th>Completed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_attempts'] as $attempt)
                                        <tr>
                                            <td>
                                                <strong>{{ $attempt->user->name }}</strong>
                                                <br><small class="text-muted">{{ $attempt->user->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($attempt->quiz->title, 30) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $attempt->quiz->category->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $attempt->score }}%
                                                </span>
                                            </td>
                                            <td>
                                                {{ $attempt->correct_answers }}/{{ $attempt->total_questions }}
                                            </td>
                                            <td>
                                                @if($attempt->duration)
                                                    {{ $attempt->duration }} min
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {{ $attempt->completed_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('quiz.result', ['quiz' => $attempt->quiz, 'attempt' => $attempt]) }}" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <h5 class="text-muted">No quiz attempts yet</h5>
                            <p class="text-muted">Quiz attempts will appear here once users start taking quizzes.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($stats['total_attempts'] > 0)
        <!-- Performance Analytics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-pie-chart"></i> Score Distribution</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $excellent = $stats['recent_attempts']->where('score', '>=', 80)->count();
                            $good = $stats['recent_attempts']->whereBetween('score', [60, 79])->count();
                            $needsImprovement = $stats['recent_attempts']->where('score', '<', 60)->count();
                            $total = $stats['recent_attempts']->count();
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Excellent (80-100%)</span>
                                <span>{{ $excellent }} ({{ $total > 0 ? round(($excellent/$total)*100, 1) : 0 }}%)</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $total > 0 ? ($excellent/$total)*100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Good (60-79%)</span>
                                <span>{{ $good }} ({{ $total > 0 ? round(($good/$total)*100, 1) : 0 }}%)</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $total > 0 ? ($good/$total)*100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Needs Improvement (0-59%)</span>
                                <span>{{ $needsImprovement }} ({{ $total > 0 ? round(($needsImprovement/$total)*100, 1) : 0 }}%)</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $total > 0 ? ($needsImprovement/$total)*100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-calculator"></i> Average Scores</h5>
                    </div>
                    <div class="card-body">
                        @if($stats['popular_quizzes']->count() > 0)
                            @foreach($stats['popular_quizzes']->take(5) as $quiz)
                                @php
                                    $avgScore = $quiz->attempts()->avg('score') ?? 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ Str::limit($quiz->title, 25) }}</span>
                                        <span class="badge bg-{{ $avgScore >= 80 ? 'success' : ($avgScore >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($avgScore, 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $avgScore >= 80 ? 'success' : ($avgScore >= 60 ? 'warning' : 'danger') }}" 
                                             role="progressbar" style="width: {{ $avgScore }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No quiz data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
