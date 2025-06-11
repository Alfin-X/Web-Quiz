@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Timer and Progress Sidebar -->
        <div class="col-md-3">
            <div class="card sticky-top">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="bi bi-clock"></i> Timer</h5>
                </div>
                <div class="card-body text-center">
                    <div class="timer" id="timer">
                        <span id="minutes">00</span>:<span id="seconds">00</span>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-warning" role="progressbar" id="timeProgress" 
                             style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="bi bi-list-ol"></i> Questions</h6>
                </div>
                <div class="card-body">
                    <div class="row" id="questionNav">
                        @foreach($questions as $index => $question)
                            <div class="col-4 mb-2">
                                <button class="btn btn-outline-secondary btn-sm w-100 question-nav-btn" 
                                        data-question="{{ $index + 1 }}" data-question-id="{{ $question->id }}">
                                    {{ $index + 1 }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-success w-100" id="submitQuizBtn">
                        <i class="bi bi-check-circle"></i> Submit Quiz
                    </button>
                </div>
            </div>
        </div>

        <!-- Quiz Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ $quiz->title }}</h4>
                        <span class="badge bg-primary">Question <span id="currentQuestionNum">1</span> of {{ $questions->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($questions as $index => $question)
                        <div class="question-container" id="question-{{ $index + 1 }}" 
                             style="{{ $index === 0 ? '' : 'display: none;' }}">
                            <div class="question-card">
                                <h5 class="mb-4">{{ $question->question_text }}</h5>
                                
                                <div class="options">
                                    @foreach($question->options as $optionIndex => $option)
                                        <button type="button" 
                                                class="btn btn-outline-primary option-btn w-100 mb-3" 
                                                data-question-id="{{ $question->id }}"
                                                data-option-id="{{ $option->id }}"
                                                data-option-index="{{ $optionIndex }}">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-3">{{ chr(65 + $optionIndex) }}</span>
                                                <span>{{ $option->option_text }}</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" id="prevBtn" disabled>
                            <i class="bi bi-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit your quiz?</p>
                <p><strong>Answered:</strong> <span id="answeredCount">0</span> of {{ $questions->count() }} questions</p>
                <p class="text-warning"><i class="bi bi-exclamation-triangle"></i> You cannot change your answers after submission.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmSubmitBtn">
                    <i class="bi bi-check-circle"></i> Submit Quiz
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentQuestion = 1;
    const totalQuestions = {{ $questions->count() }};
    const timeLimit = {{ $timeRemaining }}; // in seconds
    let timeRemaining = timeLimit;
    let timer;
    const userAnswers = @json($userAnswers);
    
    // Initialize answered questions
    updateAnsweredQuestions();
    
    // Start timer
    startTimer();
    
    // Question navigation
    $('.question-nav-btn').click(function() {
        const questionNum = parseInt($(this).data('question'));
        showQuestion(questionNum);
    });
    
    // Option selection
    $('.option-btn').click(function() {
        const questionId = $(this).data('question-id');
        const optionId = $(this).data('option-id');
        
        // Remove selection from other options in this question
        $(`.option-btn[data-question-id="${questionId}"]`).removeClass('selected');
        
        // Select this option
        $(this).addClass('selected');
        
        // Save answer
        saveAnswer(questionId, optionId);
    });
    
    // Navigation buttons
    $('#prevBtn').click(function() {
        if (currentQuestion > 1) {
            showQuestion(currentQuestion - 1);
        }
    });
    
    $('#nextBtn').click(function() {
        if (currentQuestion < totalQuestions) {
            showQuestion(currentQuestion + 1);
        }
    });
    
    // Submit quiz
    $('#submitQuizBtn').click(function() {
        updateAnsweredCount();
        $('#submitModal').modal('show');
    });
    
    $('#confirmSubmitBtn').click(function() {
        submitQuiz();
    });
    
    function startTimer() {
        timer = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(timer);
                alert('Time is up! Your quiz will be submitted automatically.');
                submitQuiz();
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        $('#minutes').text(minutes.toString().padStart(2, '0'));
        $('#seconds').text(seconds.toString().padStart(2, '0'));
        
        const progressPercent = (timeRemaining / timeLimit) * 100;
        $('#timeProgress').css('width', progressPercent + '%');
        
        // Change color based on remaining time
        if (progressPercent <= 10) {
            $('#timeProgress').removeClass('bg-warning bg-danger').addClass('bg-danger');
        } else if (progressPercent <= 25) {
            $('#timeProgress').removeClass('bg-warning bg-success').addClass('bg-warning');
        }
    }
    
    function showQuestion(questionNum) {
        $('.question-container').hide();
        $(`#question-${questionNum}`).show();
        
        currentQuestion = questionNum;
        $('#currentQuestionNum').text(questionNum);
        
        // Update navigation buttons
        $('#prevBtn').prop('disabled', questionNum === 1);
        $('#nextBtn').prop('disabled', questionNum === totalQuestions);
        
        // Update question navigation
        $('.question-nav-btn').removeClass('btn-primary').addClass('btn-outline-secondary');
        $(`.question-nav-btn[data-question="${questionNum}"]`).removeClass('btn-outline-secondary').addClass('btn-primary');
    }
    
    function saveAnswer(questionId, optionId) {
        $.ajax({
            url: '{{ route("quiz.saveAnswer", $quiz) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                attempt_id: {{ $attempt->id }},
                question_id: questionId,
                option_id: optionId
            },
            success: function(response) {
                if (response.success) {
                    userAnswers[questionId] = optionId;
                    updateAnsweredQuestions();
                }
            },
            error: function(xhr) {
                console.error('Error saving answer:', xhr);
            }
        });
    }
    
    function updateAnsweredQuestions() {
        $('.question-nav-btn').each(function() {
            const questionId = $(this).data('question-id');
            if (userAnswers[questionId]) {
                $(this).removeClass('btn-outline-secondary').addClass('btn-success');
                
                // Mark the selected option
                $(`.option-btn[data-question-id="${questionId}"][data-option-id="${userAnswers[questionId]}"]`).addClass('selected');
            }
        });
    }
    
    function updateAnsweredCount() {
        const answeredCount = Object.keys(userAnswers).length;
        $('#answeredCount').text(answeredCount);
    }
    
    function submitQuiz() {
        clearInterval(timer);
        
        $.ajax({
            url: '{{ route("quiz.submit", $quiz) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                attempt_id: {{ $attempt->id }}
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Error submitting quiz: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                alert('Error submitting quiz. Please try again.');
                console.error('Submit error:', xhr);
            }
        });
    }
    
    // Prevent page refresh/close without warning
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endpush
