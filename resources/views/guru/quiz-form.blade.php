@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-journal-text"></i> 
            {{ isset($quiz) ? 'Edit Quiz' : 'Create New Quiz' }}
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guru.quizzes') }}">My Quizzes</a></li>
                <li class="breadcrumb-item active">{{ isset($quiz) ? 'Edit' : 'Create' }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-pencil"></i> Quiz Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($quiz) ? route('guru.quizzes.update', $quiz) : route('guru.quizzes.store') }}">
                        @csrf
                        @if(isset($quiz))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $quiz->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $quiz->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $quiz->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="time_limit" class="form-label">Time Limit (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                           id="time_limit" name="time_limit" min="1" max="180" 
                                           value="{{ old('time_limit', $quiz->time_limit ?? 30) }}" required>
                                    @error('time_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $quiz->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Students can take this quiz)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('guru.quizzes') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Quizzes
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> 
                                {{ isset($quiz) ? 'Update Quiz' : 'Create Quiz' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Creating Effective Quizzes:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Use clear, concise titles</li>
                        <li><i class="bi bi-check-circle text-success"></i> Provide helpful descriptions</li>
                        <li><i class="bi bi-check-circle text-success"></i> Set appropriate time limits</li>
                        <li><i class="bi bi-check-circle text-success"></i> Choose relevant categories</li>
                    </ul>

                    <hr>

                    <h6>Time Limit Suggestions:</h6>
                    <ul class="list-unstyled">
                        <li><strong>5-10 minutes:</strong> Quick review quizzes</li>
                        <li><strong>15-30 minutes:</strong> Standard assessments</li>
                        <li><strong>45-60 minutes:</strong> Comprehensive tests</li>
                        <li><strong>90+ minutes:</strong> Final examinations</li>
                    </ul>

                    @if(isset($quiz))
                        <hr>
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Next Step:</strong> After updating, you can add or modify questions for this quiz.
                        </div>
                    @else
                        <hr>
                        <div class="alert alert-success">
                            <i class="bi bi-lightbulb"></i>
                            <strong>Next Step:</strong> After creating, you'll be able to add questions to your quiz.
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($quiz))
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Quiz Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary">{{ $quiz->questions_count ?? 0 }}</h4>
                                    <small class="text-muted">Questions</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ $quiz->attempts_count ?? 0 }}</h4>
                                <small class="text-muted">Attempts</small>
                            </div>
                        </div>
                        
                        @if($quiz->questions_count > 0)
                            <div class="mt-3">
                                <a href="{{ route('guru.questions', $quiz) }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-list-ul"></i> Manage Questions
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
