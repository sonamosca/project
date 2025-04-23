{{-- resources/views/admin/events/scan.blade.php --}}
@extends('layouts.admin')

@section('title', 'Scan Voters - ' . $event->title) {{-- Dynamic Title --}}

@push('styles')
    {{-- Add styles specific to the scan page here --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Pass Event ID and URLs to JavaScript --}}
    <meta name="event-id" content="{{ $event->id }}">
    <meta name="record-vote-url" content="{{ route('admin.events.record_vote', $event->id) }}">
    <meta name="get-voters-url" content="{{ route('admin.events.get_recorded_voters', $event->id) }}">
    <style>
        /* Paste the CSS styles for your scan view here */
        /* --- Base Styles --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* --- Scan View Container --- */
        #scan-view {
            margin: 0 auto; padding: 20px; background-color: #ffffff;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* --- Scan View Header --- */
        #scan-view header {
            display: flex; flex-wrap: wrap; justify-content: space-between;
            align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee;
            padding-bottom: 15px; gap: 15px;
        }
        #scan-view header h1 { font-size: 24px; color: #333; margin: 0; font-weight: 600; word-break: break-word; }
        a.back-button-link { text-decoration: none; display: inline-block; }
        .back-button {
             padding: 7px 15px; background-color: #6c757d; color: white !important;
             border: none; border-radius: 5px; font-size: 14px; cursor: pointer;
             text-decoration: none; display: inline-flex; align-items: center;
             gap: 6px; font-weight: 500; height: 38px; white-space: nowrap;
             transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s;
        }
        .back-button:hover { background-color: #5a6268; text-decoration: none; opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }

        /* --- Scan Input Area --- */
        .scan-input-section { margin-bottom: 25px; }
        .scan-input-section label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 14px; }
        .scan-input-area { display: flex; gap: 10px; align-items: stretch; }
        #scanInput { flex-grow: 1; padding: 10px 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; height: 38px; }
        #recordVoteBtn {
            padding: 7px 15px; background-color: #28a745; color: white; border: none;
            border-radius: 5px; font-size: 14px; cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s;
            white-space: nowrap; display: inline-flex; align-items: center;
            gap: 6px; font-weight: 500; height: 38px;
        }
        #recordVoteBtn:hover { background-color: #218838; opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        #recordVoteBtn:disabled { background-color: #cccccc; cursor: not-allowed; opacity: 0.7; }

        /* --- Scan Status Message --- */
        #scanStatus { margin-top: 15px; padding: 10px 15px; border-radius: 5px; text-align: center; font-weight: 500; font-size: 14px; display: none; border: 1px solid transparent; }
        #scanStatus.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; display: block; }
        #scanStatus.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; display: block; }
        #scanStatus.info { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; display: block; }

        /* --- Voters Table Section --- */
        .voters-table-section { margin-top: 30px; }
        .voters-table-section h2 { font-size: 20px; margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; font-weight: 600; }
        .voters-table-container { border: 1px solid #dee2e6; border-radius: 6px; overflow-x: auto; background-color: #fff; }
        .voters-table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 600px; }
        .voters-table thead th { background-color: #e9ecef; padding: 10px 12px; text-align: left; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; white-space: nowrap; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .voters-table tbody td { padding: 10px 12px; border-bottom: 1px solid #eee; vertical-align: middle; word-break: break-word; color: #495057; }
        .voters-table tbody tr:last-child td { border-bottom: none; }
        .voters-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .voters-table tbody tr:hover { background-color: #f1f1f1; }
        .no-voters-row td, .loading-row td { text-align: center; padding: 25px 15px; font-style: italic; color: #6c757d; background-color: #fff !important; font-size: 15px; }
        .loading-row td i { margin-right: 8px; }

    </style>
@endpush

@section('content')
    <div id="scan-view" data-event-id="{{ $event->id }}">
         <header>
            {{-- Display the specific event's title --}}
            <h1 id="scanViewTitle">Scan Voters: {{ $event->title }}</h1>
            <a href="{{ route('admin.events.index') }}" class="back-button-link" title="Back to Event List"> {{-- Link back --}}
                <button id="backToManageBtn" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to Manage Events
                </button>
            </a>
         </header>
         <div class="scan-input-section">
             {{-- Updated label to be more generic --}}
             <label for="scanInput">Scan Barcode or Enter Voter ID:</label>
             <div class="scan-input-area">
                 <input type="text" id="scanInput" placeholder="Scan/Enter ID..." autofocus>
                 <button id="recordVoteBtn"><i class="fas fa-check"></i> Record</button>
             </div>
             <div id="scanStatus"></div>
         </div>
         <div class="voters-table-section">
             <h2>Recorded Voters (<span id="voterCount">0</span>)</h2>
              <div class="voters-table-container">
                 <table class="voters-table">
                     <thead>
                         <tr>
                            {{-- Updated table header --}}
                            <th>VoterID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Programme</th>
                            <th>Email</th>
                         </tr>
                     </thead>
                     <tbody id="recordedVotersTbody">
                         {{-- Loading/Empty state --}}
                         <tr class="loading-row">
                             <td colspan="5"><i class="fas fa-spinner fa-spin"></i> Loading recorded voters...</td>
                         </tr>
                         <tr class="no-voters-row" style="display: none;">
                             <td colspan="5">No voters recorded yet for this event.</td>
                         </tr>
                     </tbody>
                 </table>
              </div>
         </div>
    </div>
@endsection

@push('scripts')
    {{-- JavaScript for the scan page functionality --}}
    <script>
        // --- START OF JAVASCRIPT FOR scan.blade.php ---

        document.addEventListener('DOMContentLoaded', function() {

            // --- Element References ---
            const scanView = document.getElementById('scan-view');
            const scanInput = document.getElementById('scanInput');
            const recordVoteBtn = document.getElementById('recordVoteBtn');
            const scanStatusDiv = document.getElementById('scanStatus');
            const votersTbody = document.getElementById('recordedVotersTbody');
            const voterCountSpan = document.getElementById('voterCount');
            const loadingRow = votersTbody?.querySelector('.loading-row'); // Use optional chaining
            const noVotersRow = votersTbody?.querySelector('.no-voters-row'); // Use optional chaining

            // --- Get URLs and Event ID from Meta Tags ---
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            // Prefer data attribute on scanView if available, fallback to meta
            const eventId = scanView?.dataset.eventId || document.querySelector('meta[name="event-id"]')?.getAttribute('content');
            const recordVoteUrl = document.querySelector('meta[name="record-vote-url"]')?.getAttribute('content');
            const getVotersUrl = document.querySelector('meta[name="get-voters-url"]')?.getAttribute('content');

            let isSubmitting = false; // Prevent double clicks/submissions
            let recordedVoterIds = new Set(); // Keep track of displayed voter primary IDs (voter->id)

            // --- Helper Functions ---
            function escapeHTML(str) {
                if (str === null || typeof str === 'undefined') return ''; // Handle null/undefined gracefully
                const div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            function showStatus(message, type = 'info') { // types: success, error, info
                if (!scanStatusDiv) return;
                scanStatusDiv.textContent = message;
                // Use classList for cleaner class management
                scanStatusDiv.classList.remove('success', 'error', 'info'); // Remove old classes
                if (type) {
                    scanStatusDiv.classList.add(type); // Add the new class
                }
                scanStatusDiv.style.display = 'block';

                // Auto-hide non-error messages after a few seconds
                if (type !== 'error') {
                    setTimeout(() => {
                        scanStatusDiv.style.display = 'none';
                        scanStatusDiv.classList.remove(type);
                    }, 4000); // Hide after 4 seconds
                }
            }

            function clearStatus() {
                if (scanStatusDiv) {
                    scanStatusDiv.textContent = '';
                    scanStatusDiv.style.display = 'none';
                    scanStatusDiv.classList.remove('success', 'error', 'info');
                }
            }

            // --- Render Table Row ---
            // Creates the HTML for a single row in the voter table
            function createVoterRow(voter) {
                // Check for essential properties: primary key 'id' and the display 'voterID'
                if (!voter || typeof voter.id === 'undefined' || typeof voter.voterID === 'undefined') {
                     console.warn("Invalid voter data received for table row:", voter);
                     return ''; // Return empty string if data is invalid
                }

                // Add null checks for other potentially optional fields
                const voterID = escapeHTML(voter.voterID);
                const name = escapeHTML(voter.name);
                const gender = escapeHTML(voter.gender);
                const programme = escapeHTML(voter.programme);
                const email = escapeHTML(voter.email);

                return `
                    <tr data-voter-pk="${voter.id}"> // Use primary key for data attribute
                        <td>${voterID}</td>
                        <td>${name}</td>
                        <td>${gender}</td>
                        <td>${programme}</td>
                        <td>${email}</td>
                    </tr>
                `;
            }

            // --- Update Table ---
            // Clears and repopulates the table with a list of voters (used on initial load)
            function updateVotersTable(voters = []) {
                if (!votersTbody || !voterCountSpan) {
                    console.error("Table body or voter count element not found.");
                    return;
                }

                // Clear existing voter rows only (keep placeholders)
                votersTbody.querySelectorAll('tr[data-voter-pk]').forEach(row => row.remove());
                recordedVoterIds.clear(); // Reset the set tracking displayed voters

                // Hide placeholders initially
                if (loadingRow) loadingRow.style.display = 'none';
                if (noVotersRow) noVotersRow.style.display = 'none';

                if (!Array.isArray(voters) || voters.length === 0) {
                    if (noVotersRow) noVotersRow.style.display = ''; // Show 'no voters' message
                    voterCountSpan.textContent = '0';
                } else {
                    // Build rows first, then append for potentially better performance
                    let rowsHtml = '';
                    voters.forEach(voter => {
                        if (voter && typeof voter.id !== 'undefined') { // Check primary key existence
                            rowsHtml += createVoterRow(voter); // Create HTML for each voter
                            recordedVoterIds.add(voter.id.toString()); // Track the primary key
                         }
                    });
                    votersTbody.insertAdjacentHTML('afterbegin', rowsHtml); // Add all rows at once
                    voterCountSpan.textContent = voters.length; // Update count
                }
            }

            // --- Add Single Voter to Table (after successful record) ---
            // Adds one voter row to the top of the table without clearing others
             function addSingleVoterToTable(voter) {
                 if (!voter || typeof voter.id === 'undefined' || !votersTbody || !voterCountSpan) {
                     console.warn("Cannot add single voter to table, invalid data or element missing:", voter);
                     return;
                 }
                 const voterPrimaryKey = voter.id.toString();

                 // Check if this voter (by primary key) is already displayed
                 if (recordedVoterIds.has(voterPrimaryKey)) {
                     console.log(`Voter PK ${voterPrimaryKey} is already in the table.`);
                     // Optionally highlight the existing row
                     const existingRow = votersTbody.querySelector(`tr[data-voter-pk="${voterPrimaryKey}"]`);
                     if (existingRow) {
                         existingRow.style.transition = 'background-color 0.3s ease-in-out';
                         existingRow.style.backgroundColor = '#d1ecf1'; // Info color flash
                         setTimeout(() => { existingRow.style.backgroundColor = ''; }, 1000);
                     }
                     return; // Don't add duplicate row
                 }

                 // Hide 'no voters' row if it's visible
                 if (noVotersRow && noVotersRow.style.display !== 'none') {
                    noVotersRow.style.display = 'none';
                 }
                 // Hide loading row if somehow still visible
                 if (loadingRow && loadingRow.style.display !== 'none') {
                    loadingRow.style.display = 'none';
                 }

                 const newRowHtml = createVoterRow(voter); // Create the HTML for the new row
                 if (newRowHtml) { // Only insert if row HTML was successfully created
                    votersTbody.insertAdjacentHTML('afterbegin', newRowHtml); // Add new row at the top
                    recordedVoterIds.add(voterPrimaryKey); // Track the added voter by primary key
                    voterCountSpan.textContent = parseInt(voterCountSpan.textContent || '0', 10) + 1; // Increment count
                 }
             }


            // --- Fetch Initial Voters ---
            // Makes AJAX request to get already recorded voters for this event
            async function fetchRecordedVoters() {
                // Check if required elements and URL are present
                if (!getVotersUrl || !votersTbody || !loadingRow || !noVotersRow || !voterCountSpan) {
                    console.error("Cannot fetch voters: Missing URL or essential table elements.");
                     if(loadingRow) loadingRow.style.display = 'none'; // Hide loading if it exists
                     if(noVotersRow) noVotersRow.style.display = ''; // Show no voters row as fallback
                     if(voterCountSpan) voterCountSpan.textContent = '0'; // Reset count
                    return;
                }

                loadingRow.style.display = ''; // Show loading row
                noVotersRow.style.display = 'none'; // Hide no voters row

                try {
                    const response = await fetch(getVotersUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest', // Often helpful for Laravel backend checks
                            'X-CSRF-TOKEN': csrfToken // Good practice even for GET if route is auth protected
                        }
                    });

                    if (!response.ok) {
                        // Try to get error message from response body, fallback to status text
                         let errorMsg = `HTTP error! Status: ${response.status}`;
                         try {
                             const errData = await response.json();
                             errorMsg = errData.message || errorMsg;
                         } catch (e) { /* Ignore if response is not JSON */ }
                        throw new Error(errorMsg);
                    }

                    const data = await response.json();
                    updateVotersTable(data.voters || []); // Update table with fetched data

                } catch (error) {
                    console.error('Error fetching recorded voters:', error);
                    showStatus(`Could not load recorded voters: ${error.message}`, 'error');
                    // Ensure a clean fallback state in case of error
                    updateVotersTable([]); // Call update with empty array to show 'no voters' row
                } finally {
                     if(loadingRow) loadingRow.style.display = 'none'; // Always hide loading row after attempt
                }
            }

            // --- Handle Vote Recording ---
            // Makes AJAX request to record a vote when ID is submitted
            async function handleRecordVote() {
                // Prevent multiple simultaneous submissions
                if (isSubmitting || !recordVoteUrl || !scanInput || !recordVoteBtn) {
                     console.warn("Submission prevented: Already submitting or missing elements/URL.");
                     return;
                }

                const voterIdValue = scanInput.value.trim(); // Get value from input
                if (!voterIdValue) {
                    showStatus('Please enter or scan a Voter ID.', 'error');
                    scanInput.focus();
                    return;
                }

                isSubmitting = true; // Flag as submitting
                recordVoteBtn.disabled = true; // Disable button
                recordVoteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recording...'; // Show loading state
                clearStatus(); // Clear previous status messages

                try {
                    const response = await fetch(recordVoteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        // Send voterID using the correct key expected by the controller
                        body: JSON.stringify({ voterID: voterIdValue }) // <<< Use voterID key here
                    });

                    const result = await response.json(); // Assume server always sends JSON

                    if (!response.ok) {
                         // Handle errors (like 422 validation, 404 not found, 500 server error)
                         // Use message from server response if available
                         showStatus(result.message || `Error: ${response.statusText || response.status}`, 'error');
                    } else {
                        // Handle success (201 Created) or info (200 OK for already voted)
                         showStatus(result.message || 'Vote processed.', result.status || 'info'); // Use status from response

                         // Add voter to table only if server indicates success/info and provides voter data
                         if (result.voter && (result.status === 'success' || result.status === 'info')) {
                             addSingleVoterToTable(result.voter);
                         }
                    }

                } catch (error) {
                    console.error('Error recording vote:', error);
                    // Display a generic error for network or unexpected issues
                    showStatus('A network or server error occurred while recording the vote.', 'error');
                } finally {
                    // Reset submission state and UI regardless of success/failure
                    isSubmitting = false;
                    recordVoteBtn.disabled = false;
                    recordVoteBtn.innerHTML = '<i class="fas fa-check"></i> Record'; // Reset button text
                    scanInput.value = ''; // Clear input field after attempt
                    scanInput.focus(); // Set focus back to input for next scan/entry
                }
            }

            // --- Event Listeners ---
            // Record vote on button click
            recordVoteBtn?.addEventListener('click', handleRecordVote);

            // Record vote on pressing Enter in the input field
            scanInput?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent default form submission (if any)
                    handleRecordVote();
                }
            });

            // --- Initial Load ---
            // Check if we have the necessary URL before fetching
            if (eventId && getVotersUrl) {
                fetchRecordedVoters(); // Load the list of recorded voters when the page is ready
            } else {
                console.error("Event ID or Get Voters URL is missing. Cannot initialize scan page.");
                if(loadingRow) loadingRow.style.display = 'none';
                if(noVotersRow) noVotersRow.style.display = ''; // Show no voters row if setup fails
                showStatus('Page initialization failed. Missing configuration.', 'error');
            }

        }); // End DOMContentLoaded

        // --- END OF JAVASCRIPT FOR scan.blade.php ---
    </script>
@endpush