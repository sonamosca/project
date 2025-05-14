{{-- File: resources/views/admin/candidates/view_by_event.blade.php --}}
@extends('layouts.admin') {{-- Use your main admin layout --}}

@section('title', 'View Candidates by Event')

{{-- @push('styles') ... STYLES REMAIN THE SAME ... @endpush --}}
@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
<style>
    /* All previous styles */
    /* --- Base & Layout --- */
    body { font-family: system-ui, sans-serif; background-color: #f8f9fa; color: #212529; margin: 0; }
    .content-container { padding: 25px; }

    /* --- Page Header --- */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #dee2e6; }
    .page-header h1 { font-size: 1.75rem; font-weight: 600; margin: 0; color: #343a40; }
    .page-header .btn i { margin-right: .3rem; }

    /* --- Card --- */
    .card { background-color: #fff; border-radius: .375rem; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 1px solid #e9ecef; margin-bottom: 1.5rem; }
    .card-body { padding: 1.5rem; display: flex; flex-direction: column; align-items: stretch; }

    /* --- Form Elements --- */
    .form-group { margin-bottom: 1.5rem; width: 100%; box-sizing: border-box; }
    .form-group label { display: block; margin-bottom: .5rem; font-weight: 500; color: #495057; }
    .custom-select { display: block; width: 100%; padding: .5rem .75rem; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: .25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
    .custom-select:focus { border-color: #80bdff; outline: 0; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }

    /* --- Placeholder Message --- */
    .placeholder-message { padding: 20px; border: 1px dashed #ccc; border-radius: 4px; color: #6c757d; text-align: center; margin-top: 0; width: 100%; box-sizing: border-box; }

    /* --- Results Heading --- */
    h3.candidates-heading { width: 100%; box-sizing: border-box; margin-top: 1.5rem !important; margin-bottom: 1rem !important; font-size: 1.25rem; font-weight: 500; text-align: left; color: #343a40; }
    h3.candidates-heading span { color:rgb(8, 8, 8); font-weight: 600; }

    /* --- Table --- */
    .table-responsive { display: block; width: 100%; overflow-x: auto; margin-top: 0; box-sizing: border-box;}
    .table { width: 100%; margin-bottom: 1rem; color: #212529; border-collapse: collapse; background-color: #fff; }
    .table th, .table td { padding: .75rem; vertical-align: middle; text-align: left; border: 1px solid #dee2e6 !important; word-wrap: break-word; }
    .table thead th { vertical-align: middle; border-bottom-width: 2px; font-weight: 600; background-color: #343a40; color: #ffffff; border-color: #454d55; white-space: nowrap; }
    .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.03); }

    /* --- Images & Actions in Table --- */
    .candidate-image { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;}
    .text-muted { color: #6c757d !important; }
    .text-center { text-align: center !important; }

    /* --- Buttons (General) --- */
    .btn { display: inline-flex; align-items: center; justify-content: center; font-weight: 400; color: #fff !important; text-align: center; vertical-align: middle; cursor: pointer; user-select: none; background-color: #6c757d; border: 1px solid transparent; padding: .5rem 1rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem; transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out; text-decoration: none; gap: 5px; }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd;}
    .btn-secondary { background-color: #6c757d; border-color: #6c757d; }
    .btn-success { background-color: #198754; border-color: #198754; }
    .btn-danger { background-color: #dc3545; border-color: #dc3545; }
    .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
    .btn:hover { opacity: 0.85; }

    /* --- Action Buttons in Table (Small Styling) --- */
    .action-buttons { white-space: nowrap; text-align: center; }
    .action-buttons .btn { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; margin-left: 2px; margin-right: 2px; gap: 4px; }
    .action-buttons .btn i { font-size: 0.85em; vertical-align: middle; }
    .action-buttons form { display: inline-block; margin: 0; vertical-align: middle; }

    /* --- Add Candidate Button Container --- */
    .add-candidate-button-container { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #dee2e6; width: 100%; box-sizing: border-box; }

    /* --- Alerts --- */
    .alert { padding: 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
    .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
    .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

</style>
@endpush

@section('content')
<div class="content-container">
    <div class="page-header">
        <h1>View Candidates by Event</h1>
        <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body view-card-body">
            <form method="GET" action="{{ route('admin.candidates.view.index') }}" id="viewCandidatesForm">
                <div class="form-group">
                    <label for="event_id">Select Event:</label>
                    <select name="event_id" id="event_id" class="custom-select" onchange="document.getElementById('viewCandidatesForm').submit();">
                        <option value="">-- Select an Event --</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ $selectedEvent && $selectedEvent->id == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if ($selectedEvent)
                <h3 class="candidates-heading mb-3">
                    Candidate List for: <span>{{ $selectedEvent->title }}</span>
                </h3>
                @if ($candidates->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                             <thead>
                                <tr>
                                    <th>Sl. No.</th>
                                    <th>Voter ID</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Programme</th>
                                    {{-- <th>Email</th> --}} {{-- << REMOVED --}}
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($candidates as $index => $candidate)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $candidate->voter->voter_id ?? 'N/A' }}</td>
                                        <td>{{ $candidate->voter->name ?? 'N/A' }}</td>
                                        <td>{{ $candidate->voter->gender ?? 'N/A' }}</td>
                                        <td>
                                            @if($candidate->voter && $candidate->voter->programme)
                                                {{ $candidate->voter->programme->name ?? 'N/A' }}
                                            @elseif($candidate->voter && isset($candidate->voter->programme))
                                                {{ $candidate->voter->programme ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        {{-- <td>{{ $candidate->voter->email ?? 'N/A' }}</td> --}} {{-- << REMOVED --}}
                                        <td class="text-center">
                                            @if ($candidate->photo_path)
                                                <img src="{{ asset('storage/' . $candidate->photo_path) }}" alt="Photo" class="candidate-image">
                                            @else
                                                <i class="fas fa-user-circle text-muted" style="font-size: 30px;"></i>
                                            @endif
                                        </td>
                                        <td class="action-buttons">
                                            <a href="{{ route('admin.candidates.edit', $candidate->id) }}" class="btn btn-success"><i class="fas fa-edit"></i> Edit</a>
                                            <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                     <p class="placeholder-message">
                        No candidates found for this event.
                    </p>
                @endif

                <div class="add-candidate-button-container">
                    <a href="{{ route('admin.candidates.manage', ['event' => $selectedEvent->id]) }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Candidate(s) to this Event
                    </a>
                </div>

            @else
                @if(!request()->filled('event_id'))
                     <p class="placeholder-message">
                        Please select an event to view or add candidates.
                    </p>
                @endif
            @endif
        </div> {{-- End card-body --}}
    </div> {{-- End card --}}

</div> {{-- End content-container --}}
@endsection

@push('scripts')
{{-- No scripts needed for this page's core functionality --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ...
    });
</script>
@endpush