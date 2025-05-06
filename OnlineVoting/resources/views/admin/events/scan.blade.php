{{-- File: resources/views/admin/events/scan.blade.php --}}

@extends('layouts.admin') {{-- Use your existing admin layout --}}

@section('title', 'Scan Voters - ' . $event->title)

@push('styles')
    {{-- Need CSRF token for AJAX POST requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- Scan Page Specific Styles --- */

        /* Container for the scan page content */
        .scan-page-container {
            padding: 20px; /* Add some padding around the content */
        }

        /* Header Styling */
        .scan-page-container h1 {
            font-size: 24px; margin-bottom: 10px; color: #333; font-weight: 600;
        }
        .scan-page-container h1 span.event-title { /* Style the event title specifically */
            color: #0077b6; /* Example blue color */
            font-weight: bold;
        }

        /* --- General Button Styles (Needed for the back button) --- */
        /* Ideally, move these general .btn styles to your main admin.css */
        .btn { padding: 7px 15px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none; color: white !important; line-height: 1.5; height: 38px; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); text-decoration: none; }
        .btn i { font-size: 1em; }
        .btn-secondary { background-color: #6c757d; }
         /* --- End General Button Styles --- */


        /* Input Area Styling */
        .scan-input-wrapper { /* Add this wrapper div in HTML */
            display: flex; /* Align items horizontally */
            align-items: stretch; /* Make items same height */
            gap: 10px; /* Space between elements */
            margin-bottom: 20px;
            margin-top: 25px; /* Space below back button */
        }
        .scan-input-wrapper label {
            flex-shrink: 0; /* Prevent label from shrinking */
            margin-bottom: 0; /* Remove default bottom margin */
            font-weight: 600;
            color: #555;
            font-size: 14px;
            padding: 8px 0; /* Add padding for vertical alignment */
            align-self: center; /* Center label text vertically */
        }
        .scan-input-wrapper input[type="text"] {
            flex-grow: 1; /* Allow input to take most space */
            padding: 8px 12px; font-size: 15px; border: 1px solid #ccc;
            border-radius: 5px; height: 38px; line-height: 1.5;
        }
        .scan-input-wrapper button { /* Style the record button */
             padding: 7px 15px; background-color: #28a745; color: white !important; border: none;
             border-radius: 5px; font-size: 14px; cursor: pointer; height: 38px;
             transition: background-color 0.2s; white-space: nowrap; font-weight: 500;
             flex-shrink: 0; /* Prevent button from shrinking */
        }
         .scan-input-wrapper button:hover { background-color: #218838; }
         .scan-input-wrapper button:disabled { background-color: #cccccc; cursor: not-allowed; opacity: 0.7; }

        /* Status Message Area */
        #scanMessageArea { margin-top: 15px; padding: 10px 15px; border: 1px solid transparent; display: none; border-radius: 5px; font-size: 0.9em; }
        #scanMessageArea.success { color: #0f5132; border-color: #badbcc; background-color: #d1e7dd; }
        #scanMessageArea.error { color: #842029; border-color: #f5c2c7; background-color: #f8d7da; }
        #scanMessageArea.info { color: #055160; border-color: #b6d4fe; background-color: #cfe2ff; }

        /* Recorded Voters Section Styling */
        .recorded-voters-section h2 {
            font-size: 20px; margin-top: 30px; margin-bottom: 15px; color: #333;
            font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 10px;
        }

        /* Table Styling */
        .scan-results-table-wrapper { /* Add this wrapper div in HTML */
             border: 1px solid #dee2e6; border-radius: 6px; overflow-x: auto; background-color: #fff;
        }
        #scanResultsTable {
            width: 100%; border-collapse: collapse; /* Remove internal borders if wrapper has one */
            font-size: 14px; min-width: 600px; /* Prevent excessive squishing */
        }
        #scanResultsTable thead th {
            background-color: #e9ecef; padding: 10px 12px; text-align: left;
            font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;
            white-space: nowrap; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        #scanResultsTable tbody td {
            padding: 10px 12px; border-bottom: 1px solid #eee; vertical-align: middle;
            color: #495057; word-break: break-word;
        }
        #scanResultsTable tbody tr:last-child td { border-bottom: none; }
        #scanResultsTable tbody tr:nth-child(even) { background-color: #f8f9fa; } /* Zebra striping */
        #scanResultsTable tbody tr:hover { background-color: #f1f1f1; } /* Hover effect */
        .loading-row td, .no-voters-row td {
            text-align: center; font-style: italic; color: #6c757d; padding: 25px 15px;
            background-color: #fff !important; /* Override zebra stripe */
        }
        .loading-row td i { margin-right: 8px; } /* Space for spinner */

    </style>
@endpush

@section('content')

<div class="scan-page-container"> {{-- Added wrapper class --}}
    <h1>Scan Voters: <span class="event-title">{{ $event->title }}</span></h1>
    {{-- MODIFIED PARAGRAPH and LINK below --}}
    <p style="margin-bottom: 25px;">
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary"> {{-- Applied button classes --}}
            <i class="fas fa-arrow-left"></i> {{-- Added Icon --}}
            <span>Back to Manage Events</span>
        </a>
    </p>
    {{-- END MODIFICATION --}}

    {{-- Form for input --}}
    <div class="scan-input-wrapper">
        <label for="voterIdInput">Scan/Enter Voter ID:</label>
        <form id="scanForm" onsubmit="return false;" style="display: contents;">
             @csrf
             <input type="text" id="voterIdInput" name="voter_id" placeholder="Scan or type Voter ID" required autofocus>
             <button type="submit" id="recordButton"><i class="fas fa-check"></i> Record</button>
        </form>
    </div>
     {{-- Area for displaying messages --}}
     <div id="scanMessageArea"></div>


    {{-- Recorded Voters Section --}}
    <div class="recorded-voters-section">
        <h2>Recorded Voters (<span id="voterCount">0</span>)</h2>
        <div class="scan-results-table-wrapper">
            <table class="scan-results-table" id="scanResultsTable">
                <thead>
                    <tr>
                        <th>Voter ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Programme</th>
                        <th>Class Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody id="recordedVotersTableBody">
                     <tr class="loading-row"><td colspan="7"><i class="fas fa-spinner fa-spin"></i> Loading recorded voters...</td></tr>
                     <tr class="no-voters-row" style="display: none;"><td colspan="7">No voters recorded yet.</td></tr>
                    {{-- Rows added dynamically by JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

</div> {{-- End scan-page-container --}}

@endsection

@push('scripts')
{{-- *** THE ENTIRE <script> block remains EXACTLY THE SAME as your last version *** --}}
{{-- Starting from: <script> document.addEventListener('DOMContentLoaded', function() { ... }); </script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Element References ---
        const scanForm = document.getElementById('scanForm');
        const voterIdInput = document.getElementById('voterIdInput');
        const recordButton = document.getElementById('recordButton');
        const messageArea = document.getElementById('scanMessageArea');
        const recordedVotersTableBody = document.getElementById('recordedVotersTableBody');
        const voterCountSpan = document.getElementById('voterCount');
        const loadingRow = recordedVotersTableBody?.querySelector('.loading-row');
        const noVotersRow = recordedVotersTableBody?.querySelector('.no-voters-row');

        // --- Config ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const eventId = {{ $event->id }};
        const recordUrl = `/admin/events/${eventId}/record-vote`;
        const getVotersUrl = `/admin/events/${eventId}/recorded-voters`;

        let isSubmitting = false;
        let recordedVoterPrimaryIds = new Set();

        // --- Helper: Display Messages (Using Basic Classes) ---
        function displayMessage(message, type = 'info') { // type: success, error, info
            if (!messageArea) return;
            messageArea.innerHTML = '';
            const messageDiv = document.createElement('div');
            // Add specific classes based on type
            if (type === 'success') messageDiv.className = 'success'; // Matches style block above
            else if (type === 'error') messageDiv.className = 'error'; // Matches style block above
            else messageDiv.className = 'info'; // Matches style block above
            messageDiv.textContent = message;
            messageArea.appendChild(messageDiv);
            messageArea.style.display = 'block';

            // Auto-hide non-error messages
            if (type !== 'error') {
                setTimeout(() => { messageArea.style.display = 'none'; }, 4000);
            }
        }

        // --- Helper: Clear Messages ---
        function clearMessages() {
            if (messageArea) messageArea.innerHTML = '';
            if (messageArea) messageArea.style.display = 'none';
        }

         // --- Helper: Escape HTML ---
         function escapeHTML(str) {
            if (str === null || typeof str === 'undefined') return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
         }

        // --- Helper: Add Row to Table ---
        // Uses voter.class_name as decided previously
        function addVoterToTable(voter) {
             if (!voter || typeof voter.id === 'undefined' || !recordedVotersTableBody || !voterCountSpan) return;
            const voterPrimaryKey = voter.id.toString();
            if (recordedVoterPrimaryIds.has(voterPrimaryKey)) {
                // Optional: Highlight existing row
                 const existingRow = recordedVotersTableBody.querySelector(`tr[data-voter-pk="${voterPrimaryKey}"]`);
                 if(existingRow) { /* Add temporary highlight class/style */ }
                 return;
            }

            if (noVotersRow && noVotersRow.style.display !== 'none') noVotersRow.style.display = 'none';
            if (loadingRow && loadingRow.style.display !== 'none') loadingRow.style.display = 'none';

            const newRow = recordedVotersTableBody.insertRow(0);
            newRow.setAttribute('data-voter-pk', voterPrimaryKey);

            newRow.insertCell().textContent = escapeHTML(voter.voter_id);
            newRow.insertCell().textContent = escapeHTML(voter.name);
            newRow.insertCell().textContent = escapeHTML(voter.gender); // Display Gender
            newRow.insertCell().textContent = escapeHTML(voter.programme); // Display Programme text
            newRow.insertCell().textContent = escapeHTML(voter.class_name); // Display Class Name (from relationship)
            newRow.insertCell().textContent = escapeHTML(voter.email); // Display Email
            newRow.insertCell().textContent = escapeHTML(voter.role); // Display Role

            recordedVoterPrimaryIds.add(voterPrimaryKey);
            voterCountSpan.textContent = recordedVoterPrimaryIds.size;
        }


        // --- Function to Load Initial Voters ---
        async function loadInitialVoters() {
            if (!getVotersUrl || !recordedVotersTableBody || !loadingRow || !noVotersRow || !voterCountSpan) return;
            loadingRow.style.display = ''; noVotersRow.style.display = 'none';
            recordedVotersTableBody.querySelectorAll('tr[data-voter-pk]').forEach(row => row.remove()); recordedVoterPrimaryIds.clear();

            try {
                const response = await fetch(getVotersUrl);
                if (!response.ok) {
                     let errorMsg = `HTTP error! Status: ${response.status}`;
                     try { const errData = await response.json(); errorMsg = errData.message || errorMsg; } catch (e) {}
                     throw new Error(errorMsg);
                 }
                const data = await response.json();
                if (data.voters && data.voters.length > 0) {
                    data.voters.forEach(voter => addVoterToTable(voter));
                } else {
                    noVotersRow.style.display = ''; voterCountSpan.textContent = '0';
                }
            } catch (error) {
                console.error('Error loading initial voters:', error); displayMessage(`Error loading recorded voters: ${error.message}`, 'error');
                noVotersRow.style.display = ''; voterCountSpan.textContent = '0';
            } finally {
                loadingRow.style.display = 'none';
            }
        }

        // --- Function to Handle Vote Recording Submission ---
        async function handleRecordVoteSubmit() {
            if (isSubmitting || !recordUrl || !voterIdInput || !recordButton) return;
            const voterIdValue = voterIdInput.value.trim();
            if (!voterIdValue) { displayMessage('Please enter or scan a Voter ID.', 'error'); voterIdInput.focus(); return; }
            isSubmitting = true; recordButton.disabled = true; recordButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recording...'; clearMessages();

            try {
                const response = await fetch(recordUrl, {
                    method: 'POST', headers: {'Content-Type': 'application/json','Accept': 'application/json','X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({ voter_id: voterIdValue }) // Use snake_case
                });
                const result = await response.json();
                if (!response.ok) {
                     displayMessage(result.message || `Error: ${response.statusText || response.status}`, 'error');
                } else {
                    displayMessage(result.message || 'Vote processed.', result.success ? 'success' : 'info');
                    if (result.success && result.voter) { addVoterToTable(result.voter); }
                    else if (!result.success && result.voter) { /* Optional: Handle already voted highlight */ }
                }
            } catch (error) {
                console.error('Error recording vote:', error); displayMessage('A network error occurred.', 'error');
            } finally {
                isSubmitting = false; recordButton.disabled = false; recordButton.innerHTML = '<i class="fas fa-check"></i> Record'; voterIdInput.value = ''; voterIdInput.focus();
            }
        }

        // --- Attach Event Listeners ---
        if (scanForm) { scanForm.addEventListener('submit', handleRecordVoteSubmit); }
        else if (recordButton) { recordButton.addEventListener('click', handleRecordVoteSubmit); }

        // --- Initial Load ---
        if (eventId && getVotersUrl) { loadInitialVoters(); }
        else { console.error("Cannot initialize page: Event ID or Get Voters URL missing."); displayMessage("Page configuration error.", "error"); if(loadingRow) loadingRow.style.display = 'none'; if(noVotersRow) noVotersRow.style.display = ''; }

    }); // End DOMContentLoaded
</script>
@endpush