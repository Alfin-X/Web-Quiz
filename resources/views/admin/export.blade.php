@extends('layouts.app')

@section('title', 'Export Data - Admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-download"></i> Export Quiz Data</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Export Data</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Export Options -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-gear"></i> Export Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.export.custom') }}" method="POST" id="exportForm">
                        @csrf
                        
                        <!-- Export Type -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Export Type</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" id="all_results" value="all_results" checked>
                                        <label class="form-check-label" for="all_results">
                                            <strong>All Quiz Results</strong>
                                            <div class="small text-muted">Export all completed quiz attempts</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" id="quiz_specific" value="quiz_specific">
                                        <label class="form-check-label" for="quiz_specific">
                                            <strong>Specific Quiz</strong>
                                            <div class="small text-muted">Export results for a specific quiz</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" id="category_specific" value="category_specific">
                                        <label class="form-check-label" for="category_specific">
                                            <strong>By Category</strong>
                                            <div class="small text-muted">Export results for a specific category</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" id="date_range" value="date_range">
                                        <label class="form-check-label" for="date_range">
                                            <strong>Date Range</strong>
                                            <div class="small text-muted">Export results within a date range</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quiz Selection -->
                        <div class="mb-3" id="quiz_selection" style="display: none;">
                            <label for="quiz_id" class="form-label">Select Quiz</label>
                            <select name="quiz_id" id="quiz_id" class="form-select">
                                <option value="">Choose a quiz...</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}">
                                        {{ $quiz->title }} ({{ $quiz->category->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Selection -->
                        <div class="mb-3" id="category_selection" style="display: none;">
                            <label for="category_id" class="form-label">Select Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Choose a category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Selection -->
                        <div id="date_selection" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_from" class="form-label">From Date</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_to" class="form-label">To Date</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-download"></i> Export to CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Export Information</h5>
                </div>
                <div class="card-body">
                    <h6>What's included in the export:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> User name and email</li>
                        <li><i class="bi bi-check-circle text-success"></i> Quiz title and category</li>
                        <li><i class="bi bi-check-circle text-success"></i> Score percentage</li>
                        <li><i class="bi bi-check-circle text-success"></i> Correct/Total answers</li>
                        <li><i class="bi bi-check-circle text-success"></i> Start and completion times</li>
                        <li><i class="bi bi-check-circle text-success"></i> Duration in minutes</li>
                        <li><i class="bi bi-check-circle text-success"></i> Performance level</li>
                    </ul>

                    <hr>

                    <h6>File Format:</h6>
                    <p class="small text-muted">
                        The export will be in CSV format, compatible with Excel, Google Sheets, and other spreadsheet applications.
                    </p>

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb"></i>
                        <strong>Tip:</strong> Use date range exports for periodic reports and specific quiz exports for detailed analysis.
                    </div>
                </div>
            </div>

            <!-- Quick Export Options -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="bi bi-lightning"></i> Quick Export</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.export.custom') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="export_type" value="date_range">
                            <input type="hidden" name="date_from" value="{{ now()->startOfWeek()->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ now()->endOfWeek()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-calendar-week"></i> This Week
                            </button>
                        </form>

                        <form action="{{ route('admin.export.custom') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="export_type" value="date_range">
                            <input type="hidden" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-calendar-month"></i> This Month
                            </button>
                        </form>

                        <form action="{{ route('admin.export.custom') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="export_type" value="date_range">
                            <input type="hidden" name="date_from" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-calendar-range"></i> Last 30 Days
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exports (if you want to track export history) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> Export Tips</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-file-earmark-spreadsheet fs-1 text-success"></i>
                                <h6 class="mt-2">Excel Compatible</h6>
                                <p class="small text-muted">Open directly in Microsoft Excel or Google Sheets</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-funnel fs-1 text-info"></i>
                                <h6 class="mt-2">Flexible Filtering</h6>
                                <p class="small text-muted">Export exactly the data you need with multiple filter options</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bi bi-shield-check fs-1 text-warning"></i>
                                <h6 class="mt-2">Secure Export</h6>
                                <p class="small text-muted">All exports are logged and secure for audit purposes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportTypeRadios = document.querySelectorAll('input[name="export_type"]');
    const quizSelection = document.getElementById('quiz_selection');
    const categorySelection = document.getElementById('category_selection');
    const dateSelection = document.getElementById('date_selection');

    function toggleSelections() {
        const selectedType = document.querySelector('input[name="export_type"]:checked').value;
        
        // Hide all selections first
        quizSelection.style.display = 'none';
        categorySelection.style.display = 'none';
        dateSelection.style.display = 'none';

        // Show relevant selection
        switch(selectedType) {
            case 'quiz_specific':
                quizSelection.style.display = 'block';
                break;
            case 'category_specific':
                categorySelection.style.display = 'block';
                break;
            case 'date_range':
                dateSelection.style.display = 'block';
                break;
        }
    }

    exportTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleSelections);
    });

    // Set today as default max date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').setAttribute('max', today);
    document.getElementById('date_to').setAttribute('max', today);
});
</script>
@endsection
