{{-- Extend your admin layout --}}
@extends('layouts.admin') {{-- Adjust layout name if needed --}}

@section('title', 'Event History')

@push('styles')
    {{-- Add any specific styles for the history table if needed --}}
    <style>
        .history-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .history-table th, .history-table td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; vertical-align: top; }
        .history-table th { background-color: #f7f7f7; font-weight: 600; }
        .history-table tbody tr:nth-child(odd) { background-color: #fdfdfd; }
        .history-table tbody tr:hover { background-color: #f0f0f0; }
        .description-col { max-width: 300px; word-wrap: break-word; } /* Limit description width */
        .no-history-message { text-align: center; padding: 30px; color: #777; font-style: italic; }
        .back-link { display: inline-block; margin-top: 20px; padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
        .back-link:hover { background-color: #5a6268; }
    </style>
@endpush

@section('content')
<div class="container"> {{-- Or your layout's content wrapper --}}
    <h1>Event History</h1>

    <table class="history-table">
        <thead>
            <tr>
                <th>Sl.No</th>
                <th>Title</th>
                <th class="description-col">Description</th>
                <th>Event Date</th>
                <th>Location</th>
                {{-- Add other relevant columns like 'Deleted At' if using Soft Deletes --}}
                {{-- <th>Deleted At</th> --}}
            </tr>
        </thead>
        <tbody>
            @forelse ($historyEvents as $index => $event)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $event->title ?? 'N/A' }}</td>
                    <td class="description-col">{{ $event->description ?? 'N/A' }}</td>
                    <td>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $event->location ?? 'N/A' }}</td>
                    {{-- Display soft delete date if applicable --}}
                    {{-- <td>{{ $event->deleted_at ? \Carbon\Carbon::parse($event->deleted_at)->format('Y-m-d H:i') : 'N/A' }}</td> --}}
                </tr>
            @empty
                <tr>
                    {{-- Adjust colspan to match the number of columns --}}
                    <td colspan="5" class="no-history-message">No event history found.</td>
                    {{-- <td colspan="6" class="no-history-message">No event history found.</td> --}} {{-- If using deleted_at --}}
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Link to go back to the main event management page --}}
    <a href="{{ route('admin.events.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Manage Events
    </a>

</div>
@endsection