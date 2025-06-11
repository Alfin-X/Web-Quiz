@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
                <div class="text-muted">
                    Welcome back, {{ auth()->user()->name }}!
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_quizzes'] }}</h4>
                            <p class="mb-0">Total Quizzes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-journal-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_categories'] }}</h4>
                            <p class="mb-0">Categories</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-tags fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_users'] }}</h4>
                            <p class="mb-0">Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_attempts'] }}</h4>
                            <p class="mb-0">Quiz Attempts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-play-circle fs-1"></i>
                        </div>
                    </div>
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
                            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-plus-circle"></i> Create New Quiz
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.categories') }}" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-tags"></i> Manage Categories
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.quizzes') }}" class="btn btn-info w-100 mb-2">
                                <i class="bi bi-journal-text"></i> View All Quizzes
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.statistics') }}" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-graph-up"></i> View Statistics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Quiz Attempts -->
    @if($stats['recent_attempts']->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-clock-history"></i> Recent Quiz Attempts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_attempts'] as $attempt)
                                        <tr>
                                            <td>
                                                <strong>{{ $attempt->user->name }}</strong><br>
                                                <small class="text-muted">{{ $attempt->user->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $attempt->quiz->title }}</strong><br>
                                                <small class="text-muted">{{ $attempt->quiz->category->name ?? 'No Category' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $attempt->score }}%
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</small>
                                            </td>
                                            <td>{{ $attempt->completed_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($attempt->duration)
                                                    {{ $attempt->duration }} min
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.statistics') }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> View All Statistics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-graph-up fs-1 text-muted"></i>
                        <h4 class="text-muted">No quiz attempts yet</h4>
                        <p class="text-muted">Quiz attempts will appear here once users start taking quizzes.</p>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Your First Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
