@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-gear"></i> Edit User: {{ $user->name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-fill"></i> User Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Administrator - Full system access
                                </option>
                                <option value="guru" {{ old('role', $user->role) == 'guru' ? 'selected' : '' }}>
                                    Guru/Teacher - Can create and manage quizzes
                                </option>
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                    Student - Can take quizzes and view results
                                </option>
                            </select>
                            @if($user->id === auth()->id())
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> You cannot change your own role.
                                </div>
                                <input type="hidden" name="role" value="{{ $user->role }}">
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave blank to keep current password. Must be at least 8 characters if changing.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle"></i> User Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-end">
                                <strong>Role</strong>
                                <div>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'guru')
                                        <span class="badge bg-success">Guru</span>
                                    @else
                                        <span class="badge bg-primary">Student</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <strong>Status</strong>
                            <div>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-start">
                        <small class="text-muted">
                            <strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}<br>
                            <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>

            @if($user->role === 'guru')
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Guru Statistics</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $guruStats = [
                                'quizzes' => \App\Models\Quiz::where('created_by', $user->id)->count(),
                                'questions' => \App\Models\Question::whereHas('quiz', function($q) use ($user) {
                                    $q->where('created_by', $user->id);
                                })->count(),
                                'attempts' => \App\Models\QuizAttempt::whereHas('quiz', function($q) use ($user) {
                                    $q->where('created_by', $user->id);
                                })->whereNotNull('completed_at')->count(),
                            ];
                        @endphp
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <h5 class="text-primary">{{ $guruStats['quizzes'] }}</h5>
                                    <small class="text-muted">Quizzes</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <h5 class="text-success">{{ $guruStats['questions'] }}</h5>
                                    <small class="text-muted">Questions</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h5 class="text-info">{{ $guruStats['attempts'] }}</h5>
                                <small class="text-muted">Attempts</small>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($user->role === 'user')
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Student Statistics</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $studentStats = [
                                'attempts' => \App\Models\QuizAttempt::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
                                'avg_score' => \App\Models\QuizAttempt::where('user_id', $user->id)->whereNotNull('completed_at')->avg('score') ?? 0,
                                'best_score' => \App\Models\QuizAttempt::where('user_id', $user->id)->whereNotNull('completed_at')->max('score') ?? 0,
                            ];
                        @endphp
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <h5 class="text-primary">{{ $studentStats['attempts'] }}</h5>
                                    <small class="text-muted">Attempts</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <h5 class="text-success">{{ number_format($studentStats['avg_score'], 1) }}%</h5>
                                    <small class="text-muted">Avg Score</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h5 class="text-info">{{ $studentStats['best_score'] }}%</h5>
                                <small class="text-muted">Best Score</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->id !== auth()->id())
                <div class="card mt-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Permanently delete this user account and all associated data.</p>
                        <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                            <i class="bi bi-trash"></i> Delete User Account
                        </button>
                    </div>
                </div>
            @endif
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
                <p>Are you sure you want to delete user <strong>{{ $user->name }}</strong>?</p>
                <p class="text-warning"><strong>Warning:</strong> This action cannot be undone. All user data will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>

<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
        this.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    const strength = getPasswordStrength(password);
    
    this.classList.remove('is-valid', 'is-invalid');
    
    if (strength >= 3) {
        this.classList.add('is-valid');
    } else {
        this.classList.add('is-invalid');
    }
});

function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Confirm password validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    this.classList.remove('is-valid', 'is-invalid');
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            this.classList.add('is-valid');
        } else {
            this.classList.add('is-invalid');
        }
    }
});
</script>
@endsection
