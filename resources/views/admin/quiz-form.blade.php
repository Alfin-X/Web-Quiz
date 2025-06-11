@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="bi bi-journal-text"></i>
                        {{ isset($quiz) ? 'Edit Quiz' : 'Create New Quiz' }}
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($quiz) ? route('admin.quizzes.update', $quiz) : route('admin.quizzes.store') }}">
                        @csrf
                        @if(isset($quiz))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $quiz->title ?? '') }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
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
                                    <input type="number" 
                                           class="form-control @error('time_limit') is-invalid @enderror" 
                                           id="time_limit" 
                                           name="time_limit" 
                                           value="{{ old('time_limit', $quiz->time_limit ?? 30) }}" 
                                           min="1" 
                                           max="180" 
                                           required>
                                    @error('time_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 180 minutes (3 hours)</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Enter quiz description (optional)">{{ old('description', $quiz->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $quiz->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (users can take this quiz)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.quizzes') }}" class="btn btn-secondary">
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

            @if(isset($quiz))
                <!-- Quiz Info -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle"></i> Quiz Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Questions:</strong>
                                <div class="h5 text-primary">{{ $quiz->questions()->count() }}</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Attempts:</strong>
                                <div class="h5 text-info">{{ $quiz->attempts()->count() }}</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Created:</strong>
                                <div class="text-muted">{{ $quiz->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <div>
                                    @if($quiz->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.questions', $quiz) }}" class="btn btn-outline-primary">
                                <i class="bi bi-question-circle"></i> Manage Questions
                            </a>
                            @if($quiz->attempts()->count() > 0)
                                <a href="{{ route('admin.export', $quiz) }}" class="btn btn-outline-success">
                                    <i class="bi bi-download"></i> Export Results
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from title (optional feature)
    $('#title').on('input', function() {
        // You can add slug generation logic here if needed
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        if (!$('#title').val().trim()) {
            isValid = false;
            $('#title').addClass('is-invalid');
        } else {
            $('#title').removeClass('is-invalid');
        }
        
        if (!$('#category_id').val()) {
            isValid = false;
            $('#category_id').addClass('is-invalid');
        } else {
            $('#category_id').removeClass('is-invalid');
        }
        
        if (!$('#time_limit').val() || $('#time_limit').val() < 1 || $('#time_limit').val() > 180) {
            isValid = false;
            $('#time_limit').addClass('is-invalid');
        } else {
            $('#time_limit').removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
        }
    });
});
</script>
@endpush
