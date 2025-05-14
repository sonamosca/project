@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->name)

@push('styles')
    {{-- Font Awesome for icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        /* Re-using styles from your programme example */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: #0d6efd; }
        a:hover { text-decoration: underline; }
        .form-container { padding: 25px; }
        .form-card { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 30px 40px; max-width: 600px; margin: 20px auto; }
        .form-card-header { text-align: center; font-size: 22px; font-weight: 600; color: #333; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        .form-control, select.form-control { display: block; width: 100%; padding: 10px 12px; font-size: 15px; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: 5px; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; height: 40px; }
        select.form-control { appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="none" stroke="%23343a40" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 5l6 6 6-6"/></svg>'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px 12px; padding-right: 2.5rem; }
        .form-control:focus { border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
        .is-invalid { border-color: #dc3545 !important; }
        .invalid-feedback { display: block; width: 100%; margin-top: 0.25rem; font-size: 85%; color: #dc3545; }
        .form-actions { margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn { padding: 8px 18px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none !important; color: white !important; line-height: 1.5; }
        .btn:hover { opacity: 0.85; }
        .btn i { margin-right: 5px; }
        .btn-primary { background-color: #0d6efd; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .alert { position: relative; padding: 1rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        hr.form-divider { margin: 25px 0; border: 0; border-top: 1px solid #eee; }
        .form-note { font-size: 0.875em; color: #6c757d; margin-bottom: 15px; }
    </style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-card">
        <div class="form-card-header">
            Edit User: {{ $user->name }}
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT') {{-- Important for update action --}}

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone (Optional):</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="address">Address (Optional):</label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="" disabled>-- Select Role --</option>
                    @foreach($roles as $roleValue => $roleName)
                        <option value="{{ $roleValue }}" {{ old('role', $user->role) == $roleValue ? 'selected' : '' }}>
                            {{ $roleName }}
                        </option>
                    @endforeach
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr class="form-divider">
            <p class="form-note">Leave password fields blank to keep the current password.</p>

            <div class="form-group">
                <label for="password">New Password (Optional):</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection