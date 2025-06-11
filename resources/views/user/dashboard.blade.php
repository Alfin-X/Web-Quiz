@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-house"></i> Dashboard</h1>
                <div class="text-muted">
                    Welcome back, {{ auth()->user()->name }}!
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $userStats['total_attempts'] }}</h4>
                            <p class="mb-0">Total Attempts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-play-circle fs-1"></i>
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
                            <h4>{{ $userStats['completed_quizzes'] }}</h4>
                            <p class="mb-0">Completed</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
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
                            <h4>{{ number_format($userStats['average_score'], 1) }}%</h4>
                            <p class="mb-0">Average Score</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up fs-1"></i>
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
                            <h4>{{ $quizzes->total() }}</h4>
                            <p class="mb-0">Available Quizzes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-journal-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.dashboard') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="search">Search Quizzes</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Search by title...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Quizzes -->
    <div class="row">
        <div class="col-md-12">
            <h3><i class="bi bi-journal-text"></i> Available Quizzes</h3>
            @if($quizzes->count() > 0)
                <div class="row">
                    @foreach($quizzes as $quiz)
                        <div class="col-md-4 mb-4">
                            <div class="card quiz-card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">{{ $quiz->title }}</h5>
                                    <small>{{ $quiz->category->name }}</small>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ Str::limit($quiz->description, 100) }}</p>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Questions</small>
                                            <div class="fw-bold">{{ $quiz->questions_count }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Time Limit</small>
                                            <div class="fw-bold">{{ $quiz->time_limit }}m</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Attempts</small>
                                            <div class="fw-bold">{{ $quiz->attempts_count }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('quiz.show', $quiz) }}" class="btn btn-primary w-100">
                                        <i class="bi bi-play-circle"></i> Take Quiz
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $quizzes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h4 class="text-muted">No quizzes found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Attempts -->
    @if($userStats['recent_attempts']->count() > 0)
        <div class="row mt-5">
            <div class="col-md-12">
                <h3><i class="bi bi-clock-history"></i> Recent Attempts</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userStats['recent_attempts'] as $attempt)
                                        <tr>
                                            <td>
                                                <strong>{{ $attempt->quiz->title }}</strong><br>
                                                <small class="text-muted">{{ $attempt->quiz->category->name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $attempt->score }}%
                                                </span>
                                            </td>
                                            <td>{{ $attempt->completed_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('quiz.result', ['quiz' => $attempt->quiz, 'attempt' => $attempt]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View Result
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
