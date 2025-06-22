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
                            <h4>{{ collect($categories)->sum('quiz_count') }}</h4>
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



    <!-- Available Categories -->
    <div class="row">
        <div class="col-md-12">
            <h3><i class="bi bi-grid-3x3-gap"></i> Quiz Categories</h3>
            @if($categories->count() > 0)
                <div class="row">
                    @foreach($categories as $category)
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card category-card h-100 border-{{ $category['color'] }}">
                                <div class="card-body text-center">
                                    <div class="category-icon mb-3">
                                        <i class="bi bi-{{ $category['icon'] }} fs-1 text-{{ $category['color'] }}"></i>
                                    </div>
                                    <h5 class="card-title">{{ $category['name'] }}</h5>
                                    @if($category['description'])
                                        <p class="card-text text-muted small">{{ Str::limit($category['description'], 60) }}</p>
                                    @endif

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <strong class="text-{{ $category['color'] }}">{{ $category['quiz_count'] }}</strong>
                                                <div class="small text-muted">Quizzes</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <strong class="text-{{ $category['color'] }}">{{ $category['total_attempts'] }}</strong>
                                            <div class="small text-muted">Attempts</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <span class="badge bg-{{ $category['color'] }}">{{ $category['difficulty_level'] }}</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    @if($category['quiz_count'] > 0)
                                        <a href="{{ route('user.category.quizzes', $category['id']) }}"
                                           class="btn btn-{{ $category['color'] }} w-100">
                                            <i class="bi bi-arrow-right-circle"></i> Explore Quizzes
                                        </a>
                                    @else
                                        <button class="btn btn-outline-secondary w-100" disabled>
                                            <i class="bi bi-hourglass"></i> Coming Soon
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="text-center py-5">
                    <i class="bi bi-grid-3x3-gap fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Categories Available</h5>
                    <p class="text-muted">Categories will appear here once they are created by administrators.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    @if($recentAttempts->count() > 0)
        <div class="row mt-5">
            <div class="col-md-12">
                <h3><i class="bi bi-clock-history"></i> Recent Activity</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Category</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                        <th>Performance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttempts as $attempt)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $attempt->quiz->title }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $attempt->quiz->category->name }}</span>
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
                        <div class="text-center mt-3">
                            <a href="{{ route('user.results') }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> View All Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.quiz-card.border-success {
    border-width: 2px !important;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
}

.quiz-card.border-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
}

.completed-badge {
    animation: pulse 2s infinite;
}

.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.category-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.quiz-card {
    transition: all 0.3s ease;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.badge.bg-warning.text-dark {
    animation: pulse 2s infinite;
}
</style>
@endpush
