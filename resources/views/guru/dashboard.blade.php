@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-speedometer2"></i> Guru Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div>
            <a href="{{ route('guru.quizzes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Quiz
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
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
                    <i class="bi bi-people fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_students'] }}</h4>
                    <p class="mb-0">Total Students</p>
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
                    <i class="bi bi-tags fs-1"></i>
                    <h4 class="mt-2">{{ $stats['total_categories'] }}</h4>
                    <p class="mb-0">Categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
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
                                    <i class="bi bi-plus-circle"></i> Create Quiz
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
                                <a href="{{ route('guru.statistics') }}" class="btn btn-outline-info">
                                    <i class="bi bi-bar-chart"></i> View Statistics
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('guru.leaderboard') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-trophy"></i> Leaderboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-clock-history"></i> Recent Quiz Attempts</h5>
                    <a href="{{ route('guru.statistics') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($stats['recent_attempts']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Completed At</th>
                                        <th>Performance</th>
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
                                                        <div class="fw-semibold">{{ $attempt->user->name }}</div>
                                                        <small class="text-muted">{{ $attempt->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ Str::limit($attempt->quiz->title, 30) }}</div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $attempt->score }}%</span>
                                            </td>
                                            <td>
                                                <small>{{ $attempt->completed_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($attempt->score >= 90)
                                                    <span class="badge bg-success">Excellent</span>
                                                @elseif($attempt->score >= 80)
                                                    <span class="badge bg-info">Very Good</span>
                                                @elseif($attempt->score >= 70)
                                                    <span class="badge bg-warning">Good</span>
                                                @elseif($attempt->score >= 60)
                                                    <span class="badge bg-secondary">Fair</span>
                                                @else
                                                    <span class="badge bg-danger">Needs Improvement</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No Recent Attempts</h5>
                            <p class="text-muted">Quiz attempts will appear here once students start taking your quizzes.</p>
                            <a href="{{ route('guru.quizzes.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Your First Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tips for Guru -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-lightbulb fs-1 text-primary"></i>
                    <h5 class="mt-2">Create Engaging Quizzes</h5>
                    <p class="text-muted">Design quizzes that challenge and motivate your students to learn better.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fs-1 text-success"></i>
                    <h5 class="mt-2">Track Progress</h5>
                    <p class="text-muted">Monitor student performance and identify areas that need improvement.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-trophy fs-1 text-warning"></i>
                    <h5 class="mt-2">Motivate Students</h5>
                    <p class="text-muted">Use leaderboards and achievements to encourage healthy competition.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.2s ease-in-out;
}
</style>
@endsection
