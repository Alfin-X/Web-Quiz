@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-list-ul"></i> Questions for "{{ $quiz->title }}"</h2>
            <p class="text-muted mb-0">Category: {{ $quiz->category->name }} | Time Limit: {{ $quiz->time_limit }} minutes</p>
        </div>
        <div>
            <a href="{{ route('guru.quizzes') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Quizzes
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="bi bi-plus-circle"></i> Add Question
            </button>
        </div>
    </div>

    <!-- Quiz Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $quiz->questions->count() }}</h4>
                        <small class="text-muted">Total Questions</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success">{{ $quiz->is_active ? 'Active' : 'Inactive' }}</h4>
                        <small class="text-muted">Quiz Status</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning">{{ $quiz->attempts_count ?? 0 }}</h4>
                        <small class="text-muted">Student Attempts</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-info">{{ $quiz->time_limit }}</h4>
                        <small class="text-muted">Minutes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-question-circle"></i> Questions List</h5>
        </div>
        <div class="card-body">
            @if($quiz->questions->count() > 0)
                @foreach($quiz->questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Question {{ $index + 1 }}</span>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="editQuestion({{ $question->id }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteQuestion({{ $question->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="fw-semibold">{{ $question->question_text }}</p>
                                    
                                    @if($question->image_path)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $question->image_path) }}" 
                                                 alt="Question Image" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif

                                    <div class="row">
                                        @foreach($question->options as $optionIndex => $option)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex align-items-center">
                                                    @if($option->is_correct)
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    @else
                                                        <i class="bi bi-circle me-2"></i>
                                                    @endif
                                                    <span class="{{ $option->is_correct ? 'fw-bold text-success' : '' }}">
                                                        {{ chr(65 + $optionIndex) }}. {{ $option->option_text }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <small class="text-muted">
                                            Correct Answer: 
                                            <span class="fw-bold text-success">
                                                {{ chr(65 + $question->options->where('is_correct', true)->keys()->first()) }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-question-circle fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Questions Added Yet</h5>
                    <p class="text-muted">Start adding questions to make this quiz available for students.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="bi bi-plus-circle"></i> Add Your First Question
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.questions.store', $quiz) }}" enctype="multipart/form-data">
                @csrf
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
                        <label for="question_image" class="form-label">Question Image (Optional)</label>
                        <input type="file" class="form-control @error('question_image') is-invalid @enderror" 
                               id="question_image" name="question_image" accept="image/*">
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                        @error('question_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeImagePreview()">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answer Options</label>
                        <div id="optionsContainer">
                            @for($i = 0; $i < 4; $i++)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                    <input type="text" class="form-control" name="options[]" 
                                           placeholder="Option {{ chr(65 + $i) }}" {{ $i < 2 ? 'required' : '' }}>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correct_option" class="form-label">Correct Answer</label>
                        <select class="form-select" id="correct_option" name="correct_option" required>
                            <option value="">Select correct answer</option>
                            <option value="0">A</option>
                            <option value="1">B</option>
                            <option value="2">C</option>
                            <option value="3">D</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editQuestionForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Same form fields as add modal -->
                    <div class="mb-3">
                        <label for="edit_question_text" class="form-label">Question Text</label>
                        <textarea class="form-control" id="edit_question_text" name="question_text" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_question_image" class="form-label">Question Image (Optional)</label>
                        <input type="file" class="form-control" id="edit_question_image" name="question_image" accept="image/*">
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                        
                        <!-- Current Image Display -->
                        <div id="currentImageDisplay" class="mt-2" style="display: none;">
                            <p class="small text-muted">Current image:</p>
                            <img id="currentImg" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answer Options</label>
                        <div id="editOptionsContainer">
                            @for($i = 0; $i < 4; $i++)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                    <input type="text" class="form-control" name="options[]" 
                                           id="edit_option_{{ $i }}" {{ $i < 2 ? 'required' : '' }}>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_correct_option" class="form-label">Correct Answer</label>
                        <select class="form-select" id="edit_correct_option" name="correct_option" required>
                            <option value="0">A</option>
                            <option value="1">B</option>
                            <option value="2">C</option>
                            <option value="3">D</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this question? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Question</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('question_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImagePreview() {
    document.getElementById('question_image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function editQuestion(questionId) {
    // Fetch question data and populate edit modal
    fetch(`/api/questions/${questionId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_question_text').value = data.question_text;
            
            // Populate options
            data.options.forEach((option, index) => {
                document.getElementById(`edit_option_${index}`).value = option.option_text;
                if (option.is_correct) {
                    document.getElementById('edit_correct_option').value = index;
                }
            });

            // Show current image if exists
            if (data.image_path) {
                document.getElementById('currentImg').src = `/storage/${data.image_path}`;
                document.getElementById('currentImageDisplay').style.display = 'block';
            } else {
                document.getElementById('currentImageDisplay').style.display = 'none';
            }

            // Set form action
            document.getElementById('editQuestionForm').action = `/guru/questions/${questionId}`;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('editQuestionModal')).show();
        });
}

function deleteQuestion(questionId) {
    document.getElementById('deleteForm').action = `/guru/questions/${questionId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
