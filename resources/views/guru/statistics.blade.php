@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bar-chart"></i> Statistics</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Statistics</li>
            </ol>
        </nav>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_quizzes'] }}</h4>
                    <p class="mb-0">My Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_questions'] }}</h4>
                    <p class="mb-0">Total Questions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_attempts'] }}</h4>
                    <p class="mb-0">Quiz Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_students'] }}</h4>
                    <p class="mb-0">Total Students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Quizzes and Recent Attempts -->
    <div class="row mb-4">
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
                                        <th>Attempts</th>
                                        <th>Popularity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['popular_quizzes'] as $quiz)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ Str::limit($quiz->title, 30) }}</div>
                                                <small class="text-muted">{{ $quiz->category->name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $quiz->attempts_count }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $maxAttempts = $stats['popular_quizzes']->max('attempts_count');
                                                    $percentage = $maxAttempts > 0 ? ($quiz->attempts_count / $maxAttempts) * 100 : 0;
                                                @endphp
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-star fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No quiz attempts yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> Recent Attempts</h5>
                </div>
                <div class="card-body">
                    @if($stats['recent_attempts']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_attempts'] as $attempt)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold small">{{ Str::limit($attempt->user->name, 15) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($attempt->quiz->title, 20) }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $attempt->score }}%</span>
                                                @if($attempt->score >= 80)
                                                    <i class="bi bi-check-circle text-success"></i>
                                                @elseif($attempt->score >= 60)
                                                    <i class="bi bi-dash-circle text-warning"></i>
                                                @else
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $attempt->completed_at->format('M d') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No recent attempts</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Analysis -->
    @if($stats['total_attempts'] > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Performance Analysis</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalAttempts = $stats['total_attempts'];
                            $excellentCount = $stats['recent_attempts']->where('score', '>=', 90)->count();
                            $goodCount = $stats['recent_attempts']->where('score', '>=', 70)->where('score', '<', 90)->count();
                            $fairCount = $stats['recent_attempts']->where('score', '>=', 60)->where('score', '<', 70)->count();
                            $poorCount = $stats['recent_attempts']->where('score', '<', 60)->count();
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 text-success">{{ $excellentCount }}</div>
                                    <small class="text-muted">Excellent (90-100%)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 text-info">{{ $goodCount }}</div>
                                    <small class="text-muted">Good (70-89%)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 text-warning">{{ $fairCount }}</div>
                                    <small class="text-muted">Fair (60-69%)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 text-danger">{{ $poorCount }}</div>
                                    <small class="text-muted">Needs Improvement (<60%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('guru.quizzes.create') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle"></i> Create New Quiz
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('guru.quizzes') }}" class="btn btn-outline-success">
                                    <i class="bi bi-journal-text"></i> Manage Quizzes
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('guru.leaderboard') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-trophy"></i> View Leaderboard
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-info">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips for Improvement -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-lightbulb fs-1 text-primary"></i>
                    <h6 class="mt-2">Improve Engagement</h6>
                    <p class="text-muted small">Create more interactive quizzes with images and varied question types.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fs-1 text-success"></i>
                    <h6 class="mt-2">Monitor Progress</h6>
                    <p class="text-muted small">Regularly check student performance and provide feedback.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-trophy fs-1 text-warning"></i>
                    <h6 class="mt-2">Motivate Students</h6>
                    <p class="text-muted small">Use leaderboards and achievements to encourage participation.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}
</style>
@endsection
