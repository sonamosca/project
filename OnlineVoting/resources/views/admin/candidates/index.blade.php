{{-- File: resources/views/admin/candidates/index.blade.php --}}
@extends('layouts.admin') {{-- Your admin layout --}}
@section('title', 'Manage Candidates')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        /* Basic page styles */
        .content-container { padding: 25px; }
        .page-header-title { font-size: 1.8rem; font-weight: 600; margin-bottom: 20px; color: #333; }

        /* Card styling */
        .card {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }

        /* *** MODIFIED card-body for Flexbox centering *** */
        .card-body {
            padding: 40px 20px;
            display: flex; /* Use Flexbox */
            justify-content: center; /* Center items horizontally */
            align-items: center; /* Center items vertically (optional, but good) */
            gap: 25px; /* Space between the buttons */
            flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
        }
        /* *** End of modification *** */

        /* Button styles */
        .btn {
            padding: 12px 25px;
            font-size: 1rem;
            border-radius: 5px;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center; /* Center text/icon within button */
            gap: 8px;
            cursor: pointer;
            border: none;
            color: white !important;
            font-weight: 500;
            min-width: 260px;
            /* Removed margin here as gap is used */
            vertical-align: middle;
         }
        .btn-primary { background-color: #0d6efd; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5c636a; }
        .btn i { /* Icon styling handled by flex alignment and gap */ }

    </style>
@endpush

@section('content')
<div class="content-container">
    <h1 class="page-header-title">Manage Candidates</h1>

    <div class="card mb-4">
        <div class="card-body"> {{-- This div is now the flex container --}}

            {{-- Button 1: Add Candidates --}}
            <a href="{{ route('admin.candidates.select_event') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add Candidate(s) to Event
            </a>

            {{-- Button 2: View Candidates --}}
            <a href="{{ route('admin.candidates.view.index') }}" class="btn btn-secondary">
                <i class="fas fa-list-alt"></i> View Existing Candidates
            </a>

        </div> {{-- End card-body --}}
    </div> {{-- End card --}}
</div> {{-- End content-container --}}
@endsection

@push('scripts')
    {{-- No scripts needed for this simple page --}}
@endpush