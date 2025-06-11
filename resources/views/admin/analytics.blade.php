@extends('layouts.app')

@section('title', 'Analytics - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up"></i> Analytics Dashboard</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Analytics</li>
            </ol>
        </nav>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-collection fs-1"></i>
                    <h4 class="mt-2">{{ $totalQuizzes }}</h4>
                    <p class="mb-0">Total Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1"></i>
                    <h4 class="mt-2">{{ $totalUsers }}</h4>
                    <p class="mb-0">Active Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check fs-1"></i>
                    <h4 class="mt-2">{{ $totalAttempts }}</h4>
                    <p class="mb-0">Completed Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle fs-1"></i>
                    <h4 class="mt-2">{{ $totalQuestions }}</h4>
                    <p class="mb-0">Total Questions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Participation Trend -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up"></i> Participation Trend (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    @if($participationTrend->count() > 0)
                        <canvas id="participationTrendChart" width="400" height="200"></canvas>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No participation data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Question Difficulty Analysis -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart"></i> Question Difficulty Analysis</h5>
                    <small class="text-muted">Questions ordered by difficulty (lowest success rate first)</small>
                </div>
                <div class="card-body">
                    @if($questionDifficulty->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Total Answers</th>
                                        <th>Correct Answers</th>
                                        <th>Success Rate</th>
                                        <th>Difficulty Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questionDifficulty->take(10) as $question)
                                        @php
                                            $difficulty = 'Easy';
                                            $badgeClass = 'bg-success';
                                            if ($question->success_rate < 30) {
                                                $difficulty = 'Very Hard';
                                                $badgeClass = 'bg-danger';
                                            } elseif ($question->success_rate < 50) {
                                                $difficulty = 'Hard';
                                                $badgeClass = 'bg-warning';
                                            } elseif ($question->success_rate < 70) {
                                                $difficulty = 'Medium';
                                                $badgeClass = 'bg-info';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ Str::limit($question->question_text, 60) }}</td>
                                            <td>{{ $question->total_answers }}</td>
                                            <td>{{ $question->correct_answers }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ str_replace('bg-', 'bg-', $badgeClass) }}" 
                                                         style="width: {{ $question->success_rate }}%">
                                                        {{ number_format($question->success_rate, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $badgeClass }}">{{ $difficulty }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bar-chart fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No question data available for analysis</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance and Top Performers -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-tags"></i> Category Performance</h5>
                </div>
                <div class="card-body">
                    @if($categoryPerformance->count() > 0)
                        @foreach($categoryPerformance as $category)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ $category['name'] }}</span>
                                    <span class="badge bg-primary">{{ $category['total_attempts'] }} attempts</span>
                                </div>
                                <div class="small text-muted mb-1">
                                    {{ $category['quizzes_count'] }} quizzes • Avg Score: {{ number_format($category['avg_score'], 1) }}%
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $category['avg_score'] >= 80 ? 'success' : ($category['avg_score'] >= 60 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $category['avg_score'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-tags fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No category data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-star"></i> Top Performers</h5>
                </div>
                <div class="card-body">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers as $index => $performer)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} rounded-pill">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $performer['user']->name }}</div>
                                    <div class="small text-muted">
                                        {{ $performer['completed_quizzes'] }} quizzes completed
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ number_format($performer['avg_score'], 1) }}%</div>
                                    <div class="small text-muted">avg score</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-star fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No user performance data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Participation Trend Chart
@if($participationTrend->count() > 0)
const trendCtx = document.getElementById('participationTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($participationTrend as $trend)
                '{{ $trend->month }}',
            @endforeach
        ],
        datasets: [{
            label: 'Quiz Attempts',
            data: [
                @foreach($participationTrend as $trend)
                    {{ $trend->attempts }},
                @endforeach
            ],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif
</script>
@endsection
