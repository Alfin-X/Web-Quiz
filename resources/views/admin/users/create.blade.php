@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-plus"></i> Create New User</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User Management</a></li>
                <li class="breadcrumb-item active">Create User</li>
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
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    Administrator - Full system access
                                </option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>
                                    Guru/Teacher - Can create and manage quizzes
                                </option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                    Student - Can take quizzes and view results
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> Role Information</h5>
                </div>
                <div class="card-body">
                    <div class="role-info" id="adminInfo" style="display: none;">
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-shield-check"></i> Administrator</h6>
                            <ul class="mb-0">
                                <li>Full system access</li>
                                <li>Manage all users and roles</li>
                                <li>View all analytics and reports</li>
                                <li>System configuration</li>
                                <li>Export data</li>
                            </ul>
                        </div>
                    </div>

                    <div class="role-info" id="guruInfo" style="display: none;">
                        <div class="alert alert-success">
                            <h6><i class="bi bi-person-badge"></i> Guru/Teacher</h6>
                            <ul class="mb-0">
                                <li>Create and manage own quizzes</li>
                                <li>Add questions with images</li>
                                <li>View student performance</li>
                                <li>Access filtered analytics</li>
                                <li>Monitor quiz attempts</li>
                            </ul>
                        </div>
                    </div>

                    <div class="role-info" id="userInfo" style="display: none;">
                        <div class="alert alert-primary">
                            <h6><i class="bi bi-mortarboard"></i> Student</h6>
                            <ul class="mb-0">
                                <li>Take available quizzes</li>
                                <li>View quiz results</li>
                                <li>Access leaderboard</li>
                                <li>Track personal progress</li>
                                <li>Browse quiz categories</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-lightbulb"></i> Tips</h6>
                        <ul class="mb-0">
                            <li>Email will be automatically verified</li>
                            <li>User will receive login credentials</li>
                            <li>Role can be changed later if needed</li>
                            <li>Strong passwords are recommended</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up"></i> User Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="text-danger">{{ \App\Models\User::where('role', 'admin')->count() }}</h5>
                                <small class="text-muted">Admins</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h5 class="text-success">{{ \App\Models\User::where('role', 'guru')->count() }}</h5>
                                <small class="text-muted">Gurus</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="text-primary">{{ \App\Models\User::where('role', 'user')->count() }}</h5>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    // Hide all role info divs
    document.querySelectorAll('.role-info').forEach(div => {
        div.style.display = 'none';
    });

    // Show selected role info
    const selectedRole = this.value;
    if (selectedRole) {
        const infoDiv = document.getElementById(selectedRole + 'Info');
        if (infoDiv) {
            infoDiv.style.display = 'block';
        }
    }
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = getPasswordStrength(password);
    
    // Remove existing strength classes
    this.classList.remove('is-valid', 'is-invalid');
    
    if (password.length > 0) {
        if (strength >= 3) {
            this.classList.add('is-valid');
        } else {
            this.classList.add('is-invalid');
        }
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
