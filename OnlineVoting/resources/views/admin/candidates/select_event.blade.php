{{-- File: resources/views/admin/candidates/select_event.blade.php --}}
@extends('layouts.admin')
@section('title', 'Select Event for Candidate Management')

@push('styles')
    {{-- Font Awesome if needed by layout --}}
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" /> --}}
    <style>
        /* --- Basic Reset/Defaults --- */
        body { font-family: system-ui, sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: inherit; } /* Links inherit color by default */

        /* --- Container --- */
        .content-container { padding: 25px; }

        /* --- Card Styling --- */
        .select-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef; /* Subtle border */
        }
        .select-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px; /* Adjust padding */
            border-bottom: 1px solid #eee;
        }
        .select-card-header h1 {
            font-size: 18px; /* Slightly smaller header */
            font-weight: 600;
            margin: 0;
            color: #333;
        }
        .select-card-body {
            padding: 0; /* Remove padding for list */
        }

        /* --- Back Button --- */
        .btn-back {
            padding: 6px 12px;
            font-size: 14px;
            border: 1px solid #ced4da; /* Light border */
            border-radius: 5px;
            cursor: pointer;
            background-color: #6c757d; /* Grey background */
            color: white !important;
            transition: background-color 0.2s, border-color 0.2s;
            white-space: nowrap;
        }
        .btn-back:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            text-decoration: none;
        }

        /* --- Event List Styling --- */
        .event-selection-list { list-style: none; padding: 0; margin: 0; }
        .event-selection-list li { border-bottom: 1px solid #f1f1f1; /* Lighter separator */ }
        .event-selection-list li:last-child { border-bottom: none; }
        .event-selection-list a {
            display: block;
            padding: 15px 25px; /* Match header padding */
            text-decoration: none;
            color: #333;
            transition: background-color 0.15s ease-in-out;
            font-size: 15px;
        }
        .event-selection-list a:hover {
            background-color: #f8f9fa; /* Light hover */
            color: #0056b3; /* Link hover color */
        }
        .event-selection-list .event-date { /* Hide date for this design */
            display: none;
        }
        .no-events-message {
            text-align: center; padding: 30px 20px; font-style: italic; color: #6c757d;
        }

        /* --- Breadcrumb (Optional) --- */
        .breadcrumb { display: flex; flex-wrap: wrap; padding: 0 0 1rem 0; margin-bottom: 1rem; list-style: none; background-color: transparent; font-size: 14px; }
        .breadcrumb-item { padding-right: 0.5rem; }
        .breadcrumb-item + .breadcrumb-item::before { content: "/"; padding-right: 0.5rem; color: #6c757d; }
        .breadcrumb-item a { color: #6c757d; }
        .breadcrumb-item.active { color: #333; font-weight: 500; }

    </style>
@endpush

@section('content')
<div class="content-container">

    {{-- Optional Breadcrumb --}}
    {{-- <ol class="breadcrumb mb-4">
         <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.candidates.index') }}">Manage Candidates</a></li>
         <li class="breadcrumb-item active">Select Event</li>
    </ol> --}}

     <div class="select-card">
        {{-- Card Header --}}
        <div class="select-card-header">
            <h1>Select an Event to Manage Candidates</h1>
            {{-- Back Button --}}
            <a href="{{ route('admin.candidates.index') }}" class="btn-back"> {{-- Use new class --}}
                 Back
            </a>
        </div>

        {{-- Card Body containing the list --}}
        <div class="select-card-body">
             @if($events->isEmpty())
                <p class="no-events-message">No active events found to add candidates to.</p>
             @else
                <ul class="event-selection-list">
                     @foreach ($events as $event)
                         <li>
                             {{-- Link directly to the manage page for this event --}}
                             <a href="{{ route('admin.candidates.manage', $event->id) }}">
                                 {{ $event->title }}
                                 {{-- Removed event date display based on design --}}
                                 {{-- @if($event->event_date)<span class="event-date">({{ $event->event_date->format('Y-m-d') }})</span>@endif --}}
                             </a>
                         </li>
                     @endforeach
                </ul>
             @endif
        </div> {{-- End card-body --}}
     </div> {{-- End card --}}
</div> {{-- End container --}}
@endsection

@push('scripts')
    {{-- No scripts needed here --}}
@endpush