@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-journal-text"></i> Manage Quizzes</h1>
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Quiz
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($quizzes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Questions</th>
                                        <th>Attempts</th>
                                        <th>Time Limit</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizzes as $quiz)
                                        <tr>
                                            <td>
                                                <strong>{{ $quiz->title }}</strong>
                                                @if($quiz->description)
                                                    <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $quiz->category->name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $quiz->questions_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $quiz->attempts_count }}</span>
                                            </td>
                                            <td>{{ $quiz->time_limit }} min</td>
                                            <td>
                                                @if($quiz->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $quiz->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.questions', $quiz) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Manage Questions">
                                                        <i class="bi bi-question-circle"></i>
                                                    </a>
                                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit Quiz">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if($quiz->attempts_count > 0)
                                                        <a href="{{ route('admin.export', $quiz) }}" 
                                                           class="btn btn-sm btn-outline-success" title="Export Results">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" 
                                                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Quiz">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-journal-text fs-1 text-muted"></i>
                            <h4 class="text-muted">No quizzes found</h4>
                            <p class="text-muted">Create your first quiz to get started.</p>
                            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create New Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($quizzes->count() > 0)
        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $quizzes->count() }}</h4>
                        <p class="mb-0">Total Quizzes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $quizzes->where('is_active', true)->count() }}</h4>
                        <p class="mb-0">Active Quizzes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>{{ $quizzes->sum('questions_count') }}</h4>
                        <p class="mb-0">Total Questions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4>{{ $quizzes->sum('attempts_count') }}</h4>
                        <p class="mb-0">Total Attempts</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
