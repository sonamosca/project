{{-- File: resources/views/admin/voters/import.blade.php --}}
@extends('layouts.admin')
@section('title', 'Manage Voters Data')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        .content-container { padding: 20px; background-color: #f0f2f5; /* Light grey background for the page */ }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; /* border-bottom: 1px solid #dee2e6; */ /* Remove page header border if not desired */ }
        .page-header h1 { font-size: 1.75rem; font-weight: 600; margin: 0; color: #343a40; }

        /* Card styling to match the target image more closely */
        .card {
            background-color: #fff;
            border-radius: .25rem; /* Less rounded corners */
            box-shadow: 0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2); /* Subtle shadow */
            margin-bottom: 1.5rem;
            border: none; /* Remove default border if using shadow */
        }
        .card-body { padding: 1.25rem; } /* Consistent padding */

        /* Styles for the top import section */
        #import-section-card .card-body {
            display: flex;
            justify-content: space-between; /* Pushes choose file and import button apart */
            align-items: center;
            flex-wrap: wrap; /* Allow wrapping */
            padding: 1rem 1.25rem; /* Slightly less padding for compact look */
        }
        #import-section-card .form-group {
            margin-bottom: 0; /* Remove bottom margin when inline */
            flex-grow: 1; /* Allow file input to take space */
            margin-right: 1rem; /* Space before import button */
        }
        #import-section-card .form-control-file {
            /* Bootstrap's default styling for file input is usually fine */
            /* You can add custom styling if needed */
        }
        #import-section-card .btn-primary {
            white-space: nowrap; /* Prevent button text from wrapping */
        }


        /* General Form Elements (for manual add mostly) */
        .form-group { margin-bottom: 1rem; }
        .form-group label { font-weight: 500; display: block; margin-bottom: .5rem; font-size: 0.875rem; color: #495057;}
        .form-control, .custom-select {
            display: block; width: 100%; padding: .375rem .75rem; font-size: 0.9rem;
            line-height: 1.5; color: #495057; background-color: #fff;
            background-clip: padding-box; border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus, .custom-select:focus {
            border-color: #86b7fe; outline: 0;
            box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
        }

        .btn { display: inline-flex; align-items: center; justify-content: center; font-weight: 400; text-align: center; vertical-align: middle; cursor: pointer; user-select: none; background-color: #0d6efd; border: 1px solid #0d6efd; padding: .375rem .75rem; font-size: 0.9rem; border-radius: .25rem; text-decoration: none; color: white !important; }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd;}
        .btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca;}
        .btn i { margin-right: 0.35em; }

        .invalid-feedback { display: block; width: 100%; margin-top: .25rem; font-size: .875em; color: #dc3545; }
        .form-text { font-size: .8em; color: #6c757d; }

        /* Alert styling */
        .alert { margin-bottom: 1.5rem; display: flex; align-items: flex-start; padding: 1rem; border-radius: .25rem;}
        .alert i { margin-right: 0.75em; font-size: 1.2em; margin-top: .15em; }
        .alert div { flex-grow: 1; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
        .alert-warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
        .error-list-container { margin-top: 1rem; }
        /* ... (error-list styles remain the same from previous version) ... */

        /* Center button for manual add form */
        #manual-add-voter-form .btn-primary {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: auto; /* Let it size to content */
            padding-left: 1.5rem; /* More padding for single "Add" button */
            padding-right: 1.5rem;
            margin-top: 1.5rem;
        }
        .import-input-group {
        display: flex;          /* Use flexbox */
        align-items: stretch;   /* Make items fill height */
        position: relative;     /* For potential focus styling */
        width: 100%;            /* Take full width */
        border: 1px solid #ced4da; /* Default border */
        border-radius: .25rem;   /* Match other inputs */
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    .import-input-group:focus-within { /* Style when input inside has focus */
         border-color: #86b7fe;
         box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
    }

    .import-input-group .form-control-file {
        position: relative;
        flex-grow: 1;          /* File input takes available space */
        border: none;          /* Remove default file input border */
        outline: none;         /* Remove focus outline */
        padding: .375rem .75rem; /* Match button/input padding */
        line-height: 1.5;       /* Match button/input line height */
        margin-bottom: 0;       /* Override default */
        /* Hiding the default browser chrome for file input is complex and varies. */
        /* This provides basic alignment but might not look identical cross-browser. */
        /* For better appearance, JS solutions or styled labels are often used. */
        color: #495057; /* Show selected file name */
        min-width: 100px; /* Prevent it from getting too small */
    }
    /* Add some basic styling to ::file-selector-button if possible */
    .import-input-group .form-control-file::file-selector-button {
        padding: .375rem .75rem;
        margin: -.375rem -.75rem;
        margin-inline-end: .75rem;
        color: #495057;
        background-color: #e9ecef;
        border-inline-end-width: 1px;
        border-inline-end-style: solid;
        border-inline-end-color: inherit; /* Match group border */
        border-radius: 0; /* Keep button part square */
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    @media (prefers-reduced-motion: reduce) {
        .import-input-group .form-control-file::file-selector-button {
            transition: none;
        }
    }
    .import-input-group .form-control-file:hover::file-selector-button {
        background-color: #dde0e3;
    }


    .import-input-group .btn-primary {
        border-top-left-radius: 0;    /* Flatten inner corner */
        border-bottom-left-radius: 0; /* Flatten inner corner */
        border-left: 1px solid #ced4da; /* Add separator line */
        font-size: 0.9rem;
        padding: .375rem .75rem; /* Match input */
        white-space: nowrap;
        line-height: 1.5; /* Match input */
        height: auto; /* Let padding control height */
    }
    .import-input-group .btn-primary:focus {
         box-shadow: none; /* Remove button focus shadow if parent has one */
    }

    /* Style invalid state for the group */
    .import-input-group.is-invalid {
        border-color: #dc3545;
    }
    .import-input-group.is-invalid:focus-within {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .import-input-group.is-invalid .btn-primary {
        border-left-color: #dc3545; /* Make button separator red too */
    }
    /* === End Import Input Group Styles === */

    </style>
@endpush

@section('content')
<div class="content-container">
    {{-- Hide Page Header if your target image doesn't have one for this page --}}
    {{--
    <div class="page-header">
        <h1>Manage Voters Data</h1>
    </div>
    --}}

    {{-- Session Messages --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert"><i class="fas fa-check-circle"></i><div>{{ session('success') }}</div></div>
    @endif
    @if (session('manual_add_success'))
        <div class="alert alert-success" role="alert"><i class="fas fa-user-check"></i><div>{{ session('manual_add_success') }}</div></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i><div>{{ session('error') }}</div></div>
    @endif
    @if (session('import_errors_detailed'))
        {{-- ... (error display logic as before) ... --}}
    @endif

    {{-- Section 1: File Import (Custom CSS Layout) --}}
    <div class="card" id="import-section-card">
        <div class="card-body">
            <form action="{{ route('admin.voters.import.file.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-2"> {{-- Add bottom margin here or control with wrapper --}}
                    <label for="voter_file" class="mb-1">Select Import File</label> {{-- Adjusted label margin --}}
                    <div class="import-input-group @error('voter_file') is-invalid @enderror"> {{-- Custom wrapper & invalid state --}}
                        <input type="file" name="voter_file" id="voter_file" class="form-control-file" accept=".csv,.txt,.xls,.xlsx" required>
                        <button type="submit" class="btn btn-primary">
                             Import
                        </button>
                    </div>
                    @error('voter_file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    </div>
    {{-- Section 2: Manual Add Voter (Main form below) --}}
    <div class="card">
        {{-- <div class="card-header">Add Voter Manually</div> --}} {{-- Header removed to match image --}}
        <div class="card-body">
            <form method="POST" action="{{ route('admin.voters.store.manual') }}" id="manual-add-voter-form">
                @csrf

                <div class="form-group">
                    <label for="manual_voter_id">VoterID</label>
                    <input type="text" name="voter_id" id="manual_voter_id" class="form-control @error('voter_id') is-invalid @enderror" value="{{ old('voter_id') }}" required>
                    @error('voter_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="manual_name">Name</label>
                    <input type="text" name="name" id="manual_name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="manual_gender">Gender</label>
                    <select name="gender" id="manual_gender" class="custom-select @error('gender') is-invalid @enderror" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>-- Select Gender --</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Role field is required by validation, so it should be present --}}
                <div class="form-group">
                    <label for="manual_role">Role *</label>
                    <select name="role" id="manual_role" class="custom-select @error('role') is-invalid @enderror" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Select Role --</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="manual_programme_id">Programme</label>
                    <select name="programme_id" id="manual_programme_id" class="custom-select @error('programme_id') is-invalid @enderror">
                        <option value="">-- Select Programme --</option>
                        @if(isset($programmes) && $programmes->count() > 0)
                            @foreach($programmes as $programme)
                                <option value="{{ $programme->id }}" {{ old('programme_id') == $programme->id ? 'selected' : '' }}>
                                    {{ $programme->name }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No programmes available</option>
                        @endif
                    </select>
                    @error('programme_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="manual_email">Email ID</label>
                    <input type="email" name="email" id="manual_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    {{-- Removed the small text hint to match image --}}
                </div>

                <button type="submit" class="btn btn-primary">
                     Add
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {{-- No specific JS needed for this page's core functionality --}}
@endpush