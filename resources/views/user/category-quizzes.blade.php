@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-journal-text"></i> {{ $category->name }} Quizzes</h2>
            @if($category->description)
                <p class="text-muted mb-0">{{ $category->description }}</p>
            @endif
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Category Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="category-icon mb-2">
                        <i class="bi bi-{{ $this->getCategoryIcon($category->name) }} fs-1 text-primary"></i>
                    </div>
                    <h5>{{ $category->name }}</h5>
                </div>
                <div class="col-md-3">
                    <h4 class="text-primary">{{ $quizzes->total() }}</h4>
                    <small class="text-muted">Available Quizzes</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-success">{{ $quizzes->where('questions_count', '>', 0)->count() }}</h4>
                    <small class="text-muted">Ready to Take</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-info">{{ $quizzes->sum('questions_count') }}</h4>
                    <small class="text-muted">Total Questions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.category.quizzes', $category) }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search quizzes in {{ $category->name }}..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('user.category.quizzes', $category) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Quiz Cards -->
    @if($quizzes->count() > 0)
        <div class="row">
            @foreach($quizzes as $quiz)
                @php
                    $isCompleted = in_array($quiz->id, $userCompletedQuizzes);
                @endphp
                <div class="col-md-4 mb-4">
                    <div class="card quiz-card h-100 {{ $isCompleted ? 'border-success' : '' }}">
                        <div class="card-header {{ $isCompleted ? 'bg-success' : 'bg-primary' }} text-white position-relative">
                            <h5 class="card-title mb-0">
                                {{ $quiz->title }}
                                @if($isCompleted)
                                    <i class="bi bi-check-circle-fill ms-2"></i>
                                @endif
                            </h5>
                            <small>{{ $quiz->category->name }}</small>
                            @if($isCompleted)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                    <i class="bi bi-trophy-fill"></i> Completed
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($quiz->description)
                                <p class="card-text">{{ Str::limit($quiz->description, 100) }}</p>
                            @endif
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="border-end">
                                        <strong>{{ $quiz->questions_count }}</strong>
                                        <div class="small text-muted">Questions</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <strong>{{ $quiz->time_limit }}</strong>
                                        <div class="small text-muted">Minutes</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <strong>{{ $quiz->attempts_count ?? 0 }}</strong>
                                    <div class="small text-muted">Attempts</div>
                                </div>
                            </div>

                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> Created by {{ $quiz->creator->name }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            @if($quiz->questions_count == 0)
                                <button class="btn btn-outline-secondary w-100" disabled>
                                    <i class="bi bi-hourglass"></i> No Questions Yet
                                </button>
                            @elseif($isCompleted)
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ route('quiz.show', $quiz) }}" class="btn btn-outline-success w-100 btn-sm">
                                            <i class="bi bi-arrow-repeat"></i> Retake
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        @php
                                            $lastAttempt = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                                ->where('quiz_id', $quiz->id)
                                                ->whereNotNull('completed_at')
                                                ->latest()
                                                ->first();
                                        @endphp
                                        @if($lastAttempt)
                                            <a href="{{ route('quiz.result', ['quiz' => $quiz, 'attempt' => $lastAttempt]) }}" class="btn btn-info w-100 btn-sm">
                                                <i class="bi bi-eye"></i> View Result
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 text-center">
                                    @if($lastAttempt)
                                        <small class="text-success">
                                            <i class="bi bi-check-circle"></i> 
                                            Last Score: <strong>{{ $lastAttempt->score }}%</strong>
                                        </small>
                                    @endif
                                </div>
                            @else
                                <a href="{{ route('quiz.show', $quiz) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle"></i> Take Quiz
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $quizzes->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">
                        @if(request('search'))
                            No quizzes found for "{{ request('search') }}"
                        @else
                            No quizzes available in {{ $category->name }}
                        @endif
                    </h5>
                    <p class="text-muted">
                        @if(request('search'))
                            Try adjusting your search criteria or browse all quizzes in this category.
                        @else
                            Check back later for new quizzes in this category.
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('user.category.quizzes', $category) }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> View All {{ $category->name }} Quizzes
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Categories
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Back to Categories -->
    <div class="text-center mt-4">
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to All Categories
        </a>
    </div>
</div>

<style>
.quiz-card.border-success {
    border-width: 2px !important;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
}

.quiz-card.border-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
}

.quiz-card {
    transition: all 0.3s ease;
}

.quiz-card:hover {
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
</style>
@endsection
