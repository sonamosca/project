{{-- File: resources/views/admin/candidates/index.blade.php --}}
@extends('layouts.admin') {{-- Your admin layout --}}
@section('title', 'Manage Candidates')

@push('styles')
    {{-- Add FontAwesome if not in main layout --}}
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" /> --}}
    <style>
        /* Add basic button styles here or ensure they are in admin.css */
        .btn { padding: 8px 15px; font-size: 14px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none; color: white !important; font-weight: 500; }
        .btn-primary { background-color: #0d6efd; }
        .btn-secondary { background-color: #6c757d; }
        .btn i { margin-right: 5px; }
        .card { background-color: #fff; border: 1px solid #eee; border-radius: 8px; margin-bottom: 1.5rem; }
        .card-body { padding: 1.5rem; }
        h1.mt-4 { margin-top: 1rem !important; margin-bottom: 1.5rem; font-size: 1.75rem; font-weight: 600;}
        /* Add other necessary styles */
    </style>
@endpush

@section('content')
<div class="container-fluid px-4"> {{-- Your container --}}
    <h1 class="mt-4">Manage Candidates</h1>
    {{-- Breadcrumbs if desired --}}

    <div class="card mb-4">
        {{-- To center the content *within* the card-body, apply text-align here --}}
        <div class="card-body" style="padding: 40px 20px; text-align: center;">

            {{-- The div for buttons will now be centered by text-align on parent.
                 We use display: inline-flex to make the div behave like an inline
                 element for centering purposes, but retain flex properties internally. --}}
            <div style="display: inline-flex; justify-content: center; gap: 15px;">
                <a href="{{ route('admin.candidates.select_event') }}" class="btn btn-primary" style="min-width: 220px;">
                    <i class="fas fa-user-plus"></i> Add Candidate(s) to Event
                </a>
                <a href="{{-- route('admin.candidates.view') --}}#" class="btn btn-secondary" style="min-width: 220px;" title="Functionality not yet implemented">
                    <i class="fas fa-list"></i> View Existing Candidates
                </a>
            </div>

        </div> {{-- End card-body --}}
    </div> {{-- End card --}}
</div> {{-- End container --}}
@endsection

@push('scripts')
    {{-- No scripts needed for this simple page --}}
@endpush