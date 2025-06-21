@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-journal-text"></i> My Quizzes</h2>
        <div>
            <a href="{{ route('guru.quizzes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Quiz
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->total() }}</h4>
                    <p class="mb-0">Total Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->where('is_active', true)->count() }}</h4>
                    <p class="mb-0">Active Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-question-circle fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->sum('questions_count') }}</h4>
                    <p class="mb-0">Total Questions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1"></i>
                    <h4 class="mt-2">{{ $quizzes->sum('attempts_count') }}</h4>
                    <p class="mb-0">Total Attempts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-list"></i> Quiz List</h5>
            <span class="badge bg-info">{{ $quizzes->total() }} Total</span>
        </div>
        <div class="card-body">
            @if($quizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Quiz</th>
                                <th>Category</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $quiz->title }}</div>
                                            @if($quiz->description)
                                                <small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                            @endif
                                            <div class="mt-1">
                                                <small class="text-info">
                                                    <i class="bi bi-clock"></i> {{ $quiz->time_limit }} minutes
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $quiz->category->name }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $quiz->questions_count }}</span>
                                        @if($quiz->questions_count == 0)
                                            <br><small class="text-warning">No questions yet</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $quiz->attempts_count }}</span>
                                        @if($quiz->attempts_count > 0)
                                            <br><small class="text-success">{{ $quiz->attempts_count }} students</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($quiz->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $quiz->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('guru.questions', $quiz) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Manage Questions">
                                                <i class="bi bi-list-ul"></i>
                                            </a>
                                            <a href="{{ route('guru.quizzes.edit', $quiz) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit Quiz">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $quiz->id }})" title="Delete Quiz">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $quizzes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Quizzes Created Yet</h5>
                    <p class="text-muted">Start creating your first quiz to engage your students.</p>
                    <a href="{{ route('guru.quizzes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Your First Quiz
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Tips Section -->
    @if($quizzes->count() > 0)
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-lightbulb fs-1 text-primary"></i>
                        <h6 class="mt-2">Add More Questions</h6>
                        <p class="text-muted small">Quizzes with more questions provide better assessment.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up fs-1 text-success"></i>
                        <h6 class="mt-2">Monitor Performance</h6>
                        <p class="text-muted small">Check statistics to see how students are performing.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-share fs-1 text-warning"></i>
                        <h6 class="mt-2">Activate Quizzes</h6>
                        <p class="text-muted small">Make sure your quizzes are active for students to access.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
                <p>Are you sure you want to delete this quiz? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All questions and student attempts will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(quizId) {
    const form = document.getElementById('deleteForm');
    form.action = `/guru/quizzes/${quizId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
