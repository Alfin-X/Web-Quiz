@extends('layouts.app')

@section('title', 'Leaderboard - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-trophy"></i> Leaderboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Leaderboard</li>
            </ol>
        </nav>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-funnel"></i> Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leaderboard') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="quiz_id" class="form-label">Quiz</label>
                        <select name="quiz_id" id="quiz_id" class="form-select">
                            <option value="">All Quizzes</option>
                            @foreach($quizzes as $quiz)
                                <option value="{{ $quiz->id }}" {{ request('quiz_id') == $quiz->id ? 'selected' : '' }}>
                                    {{ $quiz->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-list-ol"></i> Rankings</h5>
            <span class="badge bg-info">{{ $leaderboard->total() }} Total Results</span>
        </div>
        <div class="card-body">
            @if($leaderboard->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Rank</th>
                                <th>User</th>
                                <th>Quiz</th>
                                <th>Category</th>
                                <th>Score</th>
                                <th>Correct/Total</th>
                                <th>Completion Time</th>
                                <th>Duration</th>
                                <th>Badge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $index => $attempt)
                                @php
                                    $rank = ($leaderboard->currentPage() - 1) * $leaderboard->perPage() + $index + 1;
                                    $duration = $attempt->started_at && $attempt->completed_at 
                                        ? $attempt->started_at->diffInMinutes($attempt->completed_at) 
                                        : 0;
                                @endphp
                                <tr class="{{ $rank <= 3 ? 'table-warning' : '' }}">
                                    <td>
                                        <span class="fw-bold fs-5">{{ $rank }}</span>
                                        @if($rank == 1)
                                            <i class="bi bi-trophy-fill text-warning ms-1"></i>
                                        @elseif($rank == 2)
                                            <i class="bi bi-award-fill text-secondary ms-1"></i>
                                        @elseif($rank == 3)
                                            <i class="bi bi-award-fill text-warning ms-1" style="color: #CD7F32 !important;"></i>
                                        @endif
                                    </td>
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
                                        <span class="badge bg-secondary">{{ $attempt->quiz->category->name }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold fs-5 me-2">{{ $attempt->score }}%</span>
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
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $attempt->completed_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $duration }} min</span>
                                    </td>
                                    <td>
                                        @if($rank == 1)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-trophy-fill"></i> 1st Place
                                            </span>
                                        @elseif($rank == 2)
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-award-fill"></i> 2nd Place
                                            </span>
                                        @elseif($rank == 3)
                                            <span class="badge" style="background-color: #CD7F32; color: white;">
                                                <i class="bi bi-award-fill"></i> 3rd Place
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $leaderboard->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trophy fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No quiz attempts found</h5>
                    <p class="text-muted">Quiz attempts will appear here once users complete quizzes.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>
@endsection
