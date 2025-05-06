{{-- File: resources/views/admin/candidates/manage.blade.php --}}
@extends('layouts.admin')
@section('title', 'Add Candidate to: ' . $event->title)

@push('styles')
    {{-- Font Awesome, CSRF, Styles... --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- Paste your existing working CSS block here --- */
        /* --- Basic Reset/Defaults --- */
        body { font-family: system-ui, sans-serif; background-color: #f4f7f6; color: #333; }
        a { text-decoration: none; color: inherit; }

        /* --- Container & Card --- */
        .content-container { padding: 25px; }
        .card { background-color: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); margin-bottom: 1.5rem; border: 1px solid #e9ecef; }
        .card-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; border-bottom: 1px solid #eee; font-weight: 600; color: #333; font-size: 16px; }
        .card-body { padding: 25px; }

        /* --- Page Header --- */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .page-header h1 { font-size: 22px; font-weight: 600; margin: 0; color: #333; }
        .page-header h1 span { color: #0d6efd; } /* Event title color */

        /* --- Buttons --- */
        .btn { padding: 8px 15px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none !important; color: white !important; line-height: 1.5; height: 38px; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .btn i { margin-right: 5px; }
        .btn-primary { background-color: #0d6efd; }
        .btn-secondary { background-color: #6c757d; }
        .btn-success { background-color: #198754; }
        .btn-danger { background-color: #dc3545; }
         .action-buttons .btn { font-size: 13px; padding: 5px 10px; height: auto; gap: 4px; }
         .action-buttons .btn i { font-size: 0.9em; margin-right: 3px; }

        /* --- Form Fields --- */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #555; font-size: 14px; }
        .form-control { display: block; width: 100%; padding: 0.5rem 0.75rem; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: 0.25rem; height: 38px; }
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback { display: block; width: 100%; margin-top: 0.25rem; font-size: 85%; color: #dc3545; }
        .form-text { font-size: 0.8em; color: #6c757d; margin-top: 4px; }

        /* --- Search Area --- */
        .search-area { display: flex; gap: 10px; align-items: flex-end; }
        .search-area .form-group { flex-grow: 1; margin-bottom: 0; }
        .search-area button { flex-shrink: 0; }

        /* --- Dynamically Added Voter Info Card --- */
        #dynamicVoterResultCard { margin-top: 1.5rem; background-color: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); border: 1px solid #e9ecef; }
        #dynamicVoterResultCard .card-header { font-size: 16px; padding: 15px 25px; border-bottom: 1px solid #eee; font-weight: 600; color: #333; }
        #dynamicVoterResultCard .card-body { padding: 25px; }
        #dynamicVoterResultCard p { margin-bottom: 8px; font-size: 14px; line-height: 1.6; }
        #dynamicVoterResultCard p strong { display: inline-block; width: 110px; color: #555; }
        #dynamicVoterResultCard .form-group { margin-bottom: 15px; }
        #dynamicVoterResultCard .form-actions { margin-top: 1rem; display: flex; justify-content: flex-end; gap: 10px; }

        /* --- NEW/MODIFIED CSS for voter detail values --- */
        #dynamicVoterResultCard p .voter-detail-value {
            color: #333; /* Ensures value text is legible, matching body text color */
            /* If you want it slightly lighter but still very readable: color: #495057; */
            /* padding-left: 5px; /* Optional: adds a small space after the label */
        }

        /* --- Alerts --- */
         .alert { position: relative; padding: 1rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
         .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
         .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

         /* Minimal styles ONLY for feedback visibility in search area */
         #searchError { color: red; margin-top: 10px; font-weight: bold; display: none;}
         #searchSpinner { margin-top: 10px; font-style: italic; display: none;}

    </style>
@endpush

@section('content')
<div class="content-container">

    {{-- Page Header --}}
    <div class="page-header">
        <h1>Manage Candidates for: <span>{{ $event->title }}</span></h1>
        <a href="{{ route('admin.candidates.select_event') }}" class="btn btn-secondary">
             <i class="fas fa-arrow-left"></i> Back to Select Event
        </a>
    </div>

    {{-- Hidden input to store current event ID for JS --}}
    <input type="hidden" id="currentEventId" value="{{ $event->id }}">

    {{-- Session Messages --}}
    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    {{-- Section to Search Candidate --}}
    <div class="card mb-4" id="searchCard">
        <div class="card-header">Search Candidate to Add</div>
        <div class="card-body">
            <div id="voterSearchArea">
                <div class="search-area">
                     <div class="form-group">
                         <label for="voterSearchInput">Enter Voter ID:</label>
                         <input type="text" id="voterSearchInput" class="form-control" placeholder="Enter ID and click Search">
                     </div>
                     <button type="button" id="searchVoterBtn" class="btn btn-primary">Search</button>
                </div>
                <div id="searchSpinner" style="display: none;">Searching...</div>
                <div id="searchError"></div> {{-- For search errors --}}
            </div>
            {{-- Voter Info and Add Form will be dynamically inserted AFTER this card --}}
            {{-- The <div id="dynamicVoterResultCard"> will be added here by JS --}}
        </div>
    </div>

    {{-- * ENTIRE "EXISTING CANDIDATES" CARD AND TABLE REMOVED * --}}

</div> {{-- End content-container --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Get Elements ---
        const searchInput = document.getElementById('voterSearchInput');
        const searchButton = document.getElementById('searchVoterBtn');
        const searchSpinner = document.getElementById('searchSpinner');
        const searchError = document.getElementById('searchError');
        const searchCard = document.getElementById('searchCard');

        // --- Config ---
        const findVoterUrl = "{{ route('admin.candidates.find_voter') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const storeCandidateUrl = "{{ route('admin.candidates.store') }}";
        const eventId = document.getElementById('currentEventId')?.value;

        // --- Function to Remove Dynamic Search Results ---
        function removeDynamicResults() {
            const existingResult = document.getElementById('dynamicVoterResultCard');
            if (existingResult) {
                existingResult.remove();
            }
             searchError.textContent = '';
             searchError.style.display = 'none';
        }

        // --- Event Listener for Search Button ---
        searchButton?.addEventListener('click', async () => {
            const voterIdToSearch = searchInput.value.trim();
            if (!voterIdToSearch) {
                 searchError.textContent = 'Please enter a Voter ID.';
                 searchError.style.display = 'block';
                 removeDynamicResults();
                 return;
             }
            if (!eventId) {
                 console.error("Event ID missing.");
                 searchError.textContent = 'Cannot search: Event context missing.';
                 searchError.style.display = 'block';
                 return;
            }

            removeDynamicResults();
            searchSpinner.style.display = 'block';

            try {
                const url = new URL(findVoterUrl);
                url.searchParams.append('voter_id', voterIdToSearch);
                url.searchParams.append('event_id', eventId);

                const headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
                if (csrfToken) { headers['X-CSRF-TOKEN'] = csrfToken; }

                const response = await fetch(url.toString(), { method: 'GET', headers: headers });
                const data = await response.json();
                searchSpinner.style.display = 'none';

                // *** Log the raw response data FOR DEBUGGING ***
                console.log('Raw response data:', data);

                if (!response.ok || !data.success) {
                    const errorMessage = (data && typeof data.message === 'string') ? data.message : `HTTP error ${response.status}`;
                    throw new Error(errorMessage);
                }
                 if (!data.voter) {
                    console.error('API success, but "voter" object is missing in the response data:', data);
                    throw new Error('Voter data structure error in response.');
                }

                // --- Success: Dynamically Create and Insert HTML Card ---
                const voter = data.voter;

                // *** Log the voter object FOR DEBUGGING ***
                console.log('Received voter object:', voter);

                const resultCard = document.createElement('div');
                resultCard.id = 'dynamicVoterResultCard';
                resultCard.className = 'card mb-4';

                // --- CORRECTED HTML structure based on database table ---
                resultCard.innerHTML = `
                    <div class="card-header">Selected Voter Details</div>
                    <div class="card-body">
                        <p><strong>Voter ID:</strong> <span class="voter-detail-value">${escapeHTML(voter.voter_id)}</span></p>
                        <p><strong>Name:</strong> <span class="voter-detail-value">${escapeHTML(voter.name)}</span></p>
                        <p><strong>Gender:</strong> <span class="voter-detail-value">${escapeHTML(voter.gender)}</span></p>
                        {{-- Use the 'programme' field from the database --}}
                        <p><strong>Programme:</strong> <span class="voter-detail-value">${escapeHTML(voter.programme)}</span></p>
                        {{-- Display 'N/A' for Class as there's no department_name field --}}
                        <p><strong>Class:</strong> <span class="voter-detail-value">N/A</span></p>
                        <p><strong>Role:</strong> <span class="voter-detail-value">${escapeHTML(voter.role)}</span></p>
                        <p><strong>Email:</strong> <span class="voter-detail-value">${escapeHTML(voter.email)}</span></p>

                        <form id="dynamicAddCandidateForm" action="${storeCandidateUrl}" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="event_id" value="${eventId}">
                            {{-- Use the 'id' field from the database for the voter's primary key --}}
                            <input type="hidden" name="voter_id" value="${escapeHTML(voter.id)}">

                            <div class="form-group mb-3">
                                <label for="candidate_photo_${voter.id}">Candidate Photo (Optional):</label>
                                <input type="file" name="photo" id="candidate_photo_${voter.id}" class="form-control" accept="image/jpeg, image/png, image/gif">
                                <div class="form-text">Upload an image file (jpg, png, gif). Max 2MB.</div>
                                <div class="invalid-feedback" data-field="photo"></div>
                                <div id="dynamicAddCandidateError" style="color: red; margin-top: 10px; font-weight: bold;"></div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Add as Candidate</button>
                                <button type="button" id="dynamicCancelBtn" class="btn btn-secondary">Cancel Search</button>
                            </div>
                        </form>
                    </div>
                `;

                // Insert the new card after the search card
                searchCard.parentNode.insertBefore(resultCard, searchCard.nextSibling);

                // Add event listener for the dynamically created Cancel button
                document.getElementById('dynamicCancelBtn')?.addEventListener('click', () => {
                     removeDynamicResults();
                     searchInput.value = ''; // Clear search input too
                     searchInput.focus();
                });

                // Add event listener for dynamic form submission
                const dynamicForm = document.getElementById('dynamicAddCandidateForm');
                dynamicForm?.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                     if (submitBtn) {
                         submitBtn.disabled = true;
                         submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                     }
                });

            } catch (error) {
                 console.error('Error during voter search or display:', error);
                 searchSpinner.style.display = 'none';
                 const displayError = (error && typeof error.message === 'string') ? error.message : 'An unknown error occurred while searching.';
                 searchError.textContent = displayError;
                 searchError.style.display = 'block';
                 removeDynamicResults();
             }
        });

        // Helper function to escape HTML, handles null/undefined by returning empty string
        function escapeHTML(str) {
            if (str === null || typeof str === 'undefined') {
                return '';
            }
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(String(str)));
            return div.innerHTML;
         }

    }); // End DOMContentLoaded
</script>
@endpush