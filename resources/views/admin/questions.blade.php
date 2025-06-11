@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="bi bi-question-circle"></i> Manage Questions</h1>
                    <p class="text-muted mb-0">Quiz: <strong>{{ $quiz->title }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('admin.quizzes') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Quizzes
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bi bi-plus-circle"></i> Add Question
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Category:</strong> {{ $quiz->category->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes
                        </div>
                        <div class="col-md-3">
                            <strong>Questions:</strong> {{ $questions->count() }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong> 
                            @if($quiz->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="row">
        <div class="col-md-12">
            @if($questions->count() > 0)
                @foreach($questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editQuestion({{ $question->id }}, '{{ addslashes($question->question_text) }}', {{ $question->options->toJson() }})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" 
                                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold">{{ $question->question_text }}</p>
                            <div class="row">
                                @foreach($question->options as $optionIndex => $option)
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 rounded border {{ $option->is_correct ? 'border-success bg-success bg-opacity-10' : 'border-light' }}">
                                            <div class="d-flex align-items-center">
                                                @if($option->is_correct)
                                                    <i class="bi bi-check-circle text-success me-2"></i>
                                                @else
                                                    <span class="me-2">○</span>
                                                @endif
                                                <span class="badge bg-secondary me-2">{{ chr(65 + $optionIndex) }}</span>
                                                <span>{{ $option->option_text }}</span>
                                                @if($option->is_correct)
                                                    <span class="badge bg-success ms-auto">Correct</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-question-circle fs-1 text-muted"></i>
                        <h4 class="text-muted">No questions found</h4>
                        <p class="text-muted">Add your first question to get started.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="bi bi-plus-circle"></i> Add Question
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.questions.store', $quiz) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text</label>
                        <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                  id="question_text" name="question_text" rows="3" required>{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Answer Options</label>
                        @for($i = 0; $i < 4; $i++)
                            <div class="input-group mb-2">
                                <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                <input type="text" class="form-control @error('options.'.$i) is-invalid @enderror" 
                                       name="options[{{ $i }}]" value="{{ old('options.'.$i) }}" 
                                       placeholder="Option {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                                <div class="input-group-text">
                                    <input class="form-check-input" type="radio" name="correct_option" 
                                           value="{{ $i }}" {{ old('correct_option') == $i ? 'checked' : '' }} required>
                                </div>
                                @error('options.'.$i)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endfor
                        <small class="form-text text-muted">Select the radio button next to the correct answer. At least 2 options are required.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editQuestionForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_question_text" class="form-label">Question Text</label>
                        <textarea class="form-control" id="edit_question_text" name="question_text" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Answer Options</label>
                        @for($i = 0; $i < 4; $i++)
                            <div class="input-group mb-2">
                                <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                <input type="text" class="form-control" name="options[{{ $i }}]" 
                                       id="edit_option_{{ $i }}" placeholder="Option {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                                <div class="input-group-text">
                                    <input class="form-check-input" type="radio" name="correct_option" 
                                           value="{{ $i }}" id="edit_correct_{{ $i }}" required>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editQuestion(id, questionText, options) {
    $('#edit_question_text').val(questionText);
    
    // Clear all options first
    for(let i = 0; i < 4; i++) {
        $('#edit_option_' + i).val('');
        $('#edit_correct_' + i).prop('checked', false);
    }
    
    // Fill options
    options.forEach(function(option, index) {
        $('#edit_option_' + index).val(option.option_text);
        if(option.is_correct) {
            $('#edit_correct_' + index).prop('checked', true);
        }
    });
    
    $('#editQuestionForm').attr('action', '{{ route("admin.questions.update", ":id") }}'.replace(':id', id));
    $('#editQuestionModal').modal('show');
}

// Show add modal if there are validation errors
@if($errors->any() && old('question_text'))
    $(document).ready(function() {
        $('#addQuestionModal').modal('show');
    });
@endif
</script>
@endpush
