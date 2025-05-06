{{-- File: resources/views/admin/programmes/create.blade.php --}}

@extends('layouts.admin') {{-- Your admin layout --}}

@section('title', 'Add New Programme')

@push('styles')
    {{-- Font Awesome for icons (ensure it's loaded in your main layout or uncomment) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
     <style>
        /* --- Basic Reset/Defaults --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: #0d6efd; }
        a:hover { text-decoration: underline; }

         /* --- Container & Card --- */
        .form-container { padding: 25px; } /* Padding around the card */
        .form-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px 40px; /* Padding inside the card */
            max-width: 600px; /* Max width for the form card */
            margin: 20px auto; /* Center the card */
        }
        .form-card-header {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        /* --- Form Fields --- */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        /* --- Apply styles to select as well --- */
        .form-control, select.form-control {
            display: block;
            width: 100%;
            padding: 10px 12px;
            font-size: 15px;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            height: 40px;
        }
         /* --- Ensure select arrow is visible --- */
        select.form-control {
             appearance: none; /* Override default */
             -webkit-appearance: none;
             -moz-appearance: none;
             background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="none" stroke="%23343a40" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 5l6 6 6-6"/></svg>'); /* Simple arrow */
             background-repeat: no-repeat;
             background-position: right 0.75rem center;
             background-size: 16px 12px;
             padding-right: 2.5rem; /* Space for arrow */
         }

        .form-control:focus {
             border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback { display: block; width: 100%; margin-top: 0.25rem; font-size: 85%; color: #dc3545; }

        /* --- Buttons --- */
        .form-actions {
            margin-top: 30px;
            text-align: right; /* Align buttons right */
            display: flex;
            justify-content: flex-end; /* Push buttons to the end */
            gap: 10px; /* Space between buttons */
        }
        .btn { padding: 8px 18px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none !important; color: white !important; line-height: 1.5; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .btn i { margin-right: 5px; }
        .btn-primary { background-color: #0d6efd; } /* Blue */
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-secondary { background-color: #6c757d; } /* Grey */
        .btn-secondary:hover { background-color: #5a6268; }

         /* --- Alerts --- */
         .alert { position: relative; padding: 1rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
         .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

     </style>
@endpush

@section('content')
<div class="form-container">

    {{-- Optional Breadcrumb --}}
    {{-- <ol class="breadcrumb mb-4">
         <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.programmes.index') }}">Programmes</a></li>
         <li class="breadcrumb-item active">Add New</li>
    </ol> --}}

    <div class="form-card">
        <div class="form-card-header">
            Add New Programme
        </div>

        {{-- Display Validation Errors if any --}}
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

        <form action="{{ route('admin.programmes.store') }}" method="POST">
            @csrf {{-- CSRF Protection --}}

            {{-- Programme Name Input --}}
            <div class="form-group">
                <label for="name">Programme Name:</label>
                <input type="text"
                       name="name"
                       id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="e.g., Computer System and Network"
                       required
                       autofocus>
                {{-- Show validation error specific to 'name' --}}
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- *** ADDED THIS BLOCK: *** --}}
            {{-- Department Selection Dropdown --}}
            <div class="form-group">
                <label for="department_id">Belongs to Department:</label>
                {{-- Added form-control class to select --}}
                <select name="department_id"
                        id="department_id"
                        class="form-control @error('department_id') is-invalid @enderror"
                        required>
                    <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>-- Select Department --</option>
                    {{-- Loop through departments passed from controller --}}
                    @isset($departments)
                        @forelse($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @empty
                            <option value="" disabled>No departments available. Please add one first.</option>
                        @endforelse
                    @else
                         <option value="" disabled>Department data unavailable.</option>
                    @endisset
                </select>
                 @error('department_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
             {{-- *** END OF ADDED BLOCK *** --}}

            {{-- Form Action Buttons --}}
            <div class="form-actions">
                <a href="{{ route('admin.programmes.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Programme
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
    {{-- No specific JS needed for this simple form, but stack is available --}}
@endpush