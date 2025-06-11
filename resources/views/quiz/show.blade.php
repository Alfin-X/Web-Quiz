@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-journal-text"></i> {{ $quiz->title }}</h3>
                    <small>Category: {{ $quiz->category->name }}</small>
                </div>
                <div class="card-body">
                    @if($quiz->description)
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted">{{ $quiz->description }}</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="bi bi-question-circle fs-1 text-primary"></i>
                                <h4>{{ $quiz->questions->count() }}</h4>
                                <p class="text-muted mb-0">Questions</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="bi bi-clock fs-1 text-warning"></i>
                                <h4>{{ $quiz->time_limit }}</h4>
                                <p class="text-muted mb-0">Minutes</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="bi bi-people fs-1 text-info"></i>
                                <h4>{{ $quiz->attempts->count() }}</h4>
                                <p class="text-muted mb-0">Attempts</p>
                            </div>
                        </div>
                    </div>

                    @if($existingAttempt)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>You have already completed this quiz!</strong><br>
                            Your score: <strong>{{ $existingAttempt->score }}%</strong> 
                            ({{ $existingAttempt->correct_answers }}/{{ $existingAttempt->total_questions }} correct)
                            <br>
                            Completed on: {{ $existingAttempt->completed_at->format('M d, Y H:i') }}
                        </div>
                        <div class="text-center">
                            <a href="{{ route('quiz.result', ['quiz' => $quiz, 'attempt' => $existingAttempt]) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-eye"></i> View Results
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>You have <strong>{{ $quiz->time_limit }} minutes</strong> to complete this quiz</li>
                                <li>Once started, the timer cannot be paused</li>
                                <li>You can only take this quiz once</li>
                                <li>Make sure you have a stable internet connection</li>
                                <li>Click "Start Quiz" when you're ready to begin</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-success btn-lg" id="startQuizBtn">
                                <i class="bi bi-play-circle"></i> Start Quiz
                            </button>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($quiz->questions->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="bi bi-list-ol"></i> Quiz Preview</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">This quiz contains {{ $quiz->questions->count() }} questions covering various topics in {{ $quiz->category->name }}.</p>
                        
                        @if($quiz->average_score > 0)
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Average Score:</small>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $quiz->average_score }}%" 
                                             aria-valuenow="{{ $quiz->average_score }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($quiz->average_score, 1) }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Difficulty Level:</small>
                                    <div>
                                        @if($quiz->average_score >= 80)
                                            <span class="badge bg-success">Easy</span>
                                        @elseif($quiz->average_score >= 60)
                                            <span class="badge bg-warning">Medium</span>
                                        @else
                                            <span class="badge bg-danger">Hard</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Starting your quiz...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#startQuizBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show loading state
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Starting...');
        $('#loadingModal').modal('show');
        
        // Start quiz
        $.ajax({
            url: '{{ route("quiz.start", $quiz) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error starting quiz: ' + (response.message || 'Unknown error'));
                    btn.prop('disabled', false).html(originalText);
                    $('#loadingModal').modal('hide');
                }
            },
            error: function(xhr) {
                let message = 'Error starting quiz';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
                btn.prop('disabled', false).html(originalText);
                $('#loadingModal').modal('hide');
            }
        });
    });
});
</script>
@endpush
