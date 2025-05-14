@extends('layouts.admin')

@section('title', 'Manage Users')

@push('styles')
    {{-- Font Awesome for icons (ensure it's loaded in your main layout or uncomment) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        /* --- Basic Reset/Defaults --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: #0d6efd; }
        a:hover { text-decoration: underline; }

        .page-container { padding: 25px; }
        .data-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px 20px; /* Adjusted padding for table */
            margin: 20px auto;
        }
        .card-header-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .card-header-action h1 { font-size: 22px; font-weight: 600; color: #333; margin: 0; }

        /* --- Table Styles --- */
        .table-responsive { overflow-x: auto; }
        .table { width: 100%; margin-bottom: 1rem; color: #212529; border-collapse: collapse; }
        .table th, .table td { padding: 0.85rem; vertical-align: top; border-top: 1px solid #dee2e6; }
        .table thead th { vertical-align: bottom; border-bottom: 2px solid #dee2e6; background-color: #f8f9fa; font-weight: 600; text-align: left;}
        .table tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.03); }
        .table tbody tr:hover { background-color: rgba(0,0,0,.06); }
        .table .actions a, .table .actions button { margin-right: 5px; }
        .table .actions form { display: inline-block; }

        /* --- Buttons --- */
        .btn { padding: 8px 18px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none !important; color: white !important; line-height: 1.5; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .btn i { margin-right: 5px; } /* Ensure icon spacing if used */
        .btn-primary { background-color: #0d6efd; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-warning { background-color: #ffc107; color: #000 !important; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: .875rem; border-radius: 0.2rem; }

        /* --- Alerts --- */
        .alert { position: relative; padding: 1rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

        /* --- Pagination --- */
        .pagination { display: flex; padding-left: 0; list-style: none; margin-top: 20px; justify-content: center; }
        .pagination .page-item .page-link { position: relative; display: block; padding: 0.5rem 0.75rem; margin-left: -1px; line-height: 1.25; color: #0d6efd; background-color: #fff; border: 1px solid #dee2e6; }
        .pagination .page-item.active .page-link { z-index: 3; color: #fff; background-color: #0d6efd; border-color: #0d6efd; }
        .pagination .page-item.disabled .page-link { color: #6c757d; pointer-events: none; background-color: #fff; border-color: #dee2e6; }
    </style>
@endpush

@section('content')
<div class="page-container">
    <div class="data-card">
        <div class="card-header-action">
            <h1>Manage Users</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New User
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ Str::title(str_replace('_', ' ', $user->role)) }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }} {{-- Laravel's default pagination --}}
        </div>
    </div>
</div>
@endsection