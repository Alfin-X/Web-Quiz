@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Result Header -->
            <div class="card mb-4">
                <div class="card-header bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }} text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-0">
                                <i class="bi bi-trophy"></i> Quiz Results: {{ $quiz->title }}
                            </h3>
                            <small>{{ $quiz->category->name }}</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <h2 class="mb-0">{{ $attempt->score }}%</h2>
                            <small>{{ $attempt->correct_answers }}/{{ $attempt->total_questions }} correct</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h5>Score</h5>
                            <div class="display-4 text-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}">
                                {{ $attempt->score }}%
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>Correct Answers</h5>
                            <div class="display-4 text-success">{{ $attempt->correct_answers }}</div>
                            <small class="text-muted">out of {{ $attempt->total_questions }}</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>Time Taken</h5>
                            <div class="display-4 text-info">
                                @if($attempt->duration)
                                    {{ $attempt->duration }}
                                @else
                                    -
                                @endif
                            </div>
                            <small class="text-muted">minutes</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5>Completed</h5>
                            <div class="h5 text-muted">{{ $attempt->completed_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $attempt->completed_at->format('H:i') }}</small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $attempt->score >= 80 ? 'success' : ($attempt->score >= 60 ? 'warning' : 'danger') }}" 
                                     role="progressbar" style="width: {{ $attempt->score }}%" 
                                     aria-valuenow="{{ $attempt->score }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $attempt->score }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        @if($attempt->score >= 80)
                            <h4 class="text-success"><i class="bi bi-emoji-smile"></i> Excellent Work!</h4>
                            <p class="text-muted">You have a great understanding of the material.</p>
                        @elseif($attempt->score >= 60)
                            <h4 class="text-warning"><i class="bi bi-emoji-neutral"></i> Good Job!</h4>
                            <p class="text-muted">You have a solid grasp of the concepts.</p>
                        @else
                            <h4 class="text-danger"><i class="bi bi-emoji-frown"></i> Keep Practicing!</h4>
                            <p class="text-muted">Review the material and try again to improve your understanding.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Question Review -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-list-check"></i> Question Review</h5>
                </div>
                <div class="card-body">
                    @foreach($attempt->userAnswers as $index => $userAnswer)
                        <div class="card mb-3 border-{{ $userAnswer->is_correct ? 'success' : 'danger' }}">
                            <div class="card-header bg-{{ $userAnswer->is_correct ? 'success' : 'danger' }} bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                                    <span class="badge bg-{{ $userAnswer->is_correct ? 'success' : 'danger' }}">
                                        {{ $userAnswer->is_correct ? 'Correct' : 'Incorrect' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="fw-bold">{{ $userAnswer->question->question_text }}</p>
                                
                                <div class="row">
                                    @foreach($userAnswer->question->options as $option)
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 rounded border
                                                @if($option->is_correct) 
                                                    border-success bg-success bg-opacity-10
                                                @elseif($userAnswer->selected_option_id == $option->id && !$option->is_correct)
                                                    border-danger bg-danger bg-opacity-10
                                                @else
                                                    border-light
                                                @endif
                                            ">
                                                <div class="d-flex align-items-center">
                                                    @if($option->is_correct)
                                                        <i class="bi bi-check-circle text-success me-2"></i>
                                                    @elseif($userAnswer->selected_option_id == $option->id && !$option->is_correct)
                                                        <i class="bi bi-x-circle text-danger me-2"></i>
                                                    @else
                                                        <span class="me-2">○</span>
                                                    @endif
                                                    
                                                    <span class="badge bg-secondary me-2">{{ chr(65 + $loop->index) }}</span>
                                                    <span>{{ $option->option_text }}</span>
                                                    
                                                    @if($userAnswer->selected_option_id == $option->id)
                                                        <span class="badge bg-primary ms-auto">Your Answer</span>
                                                    @endif
                                                    
                                                    @if($option->is_correct)
                                                        <span class="badge bg-success ms-auto">Correct Answer</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Leaderboard -->
            @if($leaderboard->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-trophy"></i> Leaderboard</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Name</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderboard as $index => $leaderAttempt)
                                        <tr class="{{ $leaderAttempt->id == $attempt->id ? 'table-warning' : '' }}">
                                            <td>
                                                @if($index < 3)
                                                    <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }}">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $leaderAttempt->user->name }}
                                                @if($leaderAttempt->id == $attempt->id)
                                                    <span class="badge bg-primary ms-1">You</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $leaderAttempt->score >= 80 ? 'success' : ($leaderAttempt->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $leaderAttempt->score }}%
                                                </span>
                                            </td>
                                            <td>{{ $leaderAttempt->completed_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i> Back to Dashboard
                </a>
                <a href="{{ route('user.results') }}" class="btn btn-outline-primary">
                    <i class="bi bi-trophy"></i> View All Results
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Results
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .navbar, .card-header { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
