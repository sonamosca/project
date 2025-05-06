{{-- Use the main admin layout file --}}
@extends('layouts.admin')

{{-- Set the title for this specific page --}}
@section('title', 'Manage Events')

{{-- Push page-specific styles into the layout's 'styles' stack --}}
@push('styles')
    {{-- Font Awesome for icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    {{-- CSRF Token for AJAX requests --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- YOUR ORIGINAL CSS - REMAINS UNCHANGED --}}
    <style>
        /* --- Base Styles --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* --- View Container --- */
        #manage-view {
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* --- Header --- */
        #manage-view header h1 {
            font-size: 24px;
            margin-bottom: 25px;
            text-align: left;
            color: #333;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
         }

        /* --- Search and Top Actions Area --- */
        .search-actions-container { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; align-items: center; }
        .search-input-group { display: flex; flex-grow: 1; min-width: 280px; }
        .search-bar { width: 100%; padding: 10px 15px; font-size: 14px; border: 1px solid #ccc; border-right: none; border-radius: 5px 0 0 5px; height: 38px; }
        .search-icon { padding: 0 15px; border: 1px solid #ccc; border-left: none; background-color: #0077b6; color: #fff; cursor: pointer; border-radius: 0 5px 5px 0; transition: background-color 0.3s; display: flex; align-items: center; justify-content: center; height: 38px; width: 45px; }
        .search-icon:hover { background-color: #005a8d; }
        .top-level-actions { display: flex; gap: 10px; flex-shrink: 0; }

        /* --- General Button Styles --- */
        .btn { padding: 7px 15px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.2s, box-shadow 0.2s, opacity 0.2s; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; text-decoration: none; color: white !important; line-height: 1.5; height: 38px; white-space: nowrap; vertical-align: middle; }
        .btn:hover { opacity: 0.85; box-shadow: 0 1px 4px rgba(0,0,0,0.1); text-decoration: none; }
        .btn i { font-size: 1em; }
        .btn-primary { background-color: #0077b6; }
        .btn-secondary { background-color: #6c757d; }
        .btn-edit { background-color: rgb(82, 124, 87); }
        .btn-delete { background-color:rgb(182, 28, 43); }

        /* --- Event List --- */
        #event-list { list-style: none; padding: 0; margin: 0; }
        #event-list li {
            margin-bottom: 10px;
            background-color: #ffffff;
            border: 1px solid #e9e9e9;
            border-radius: 6px;
            font-size: 15px;
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Push link and actions apart */
            align-items: center; /* Vertically center items */
            padding: 0;
        }

        /* * NEW CSS for the link area * */
        .event-link {
            display: block; flex-grow: 1; padding: 10px 15px;
            text-decoration: none; color: inherit; cursor: pointer;
            border-radius: 6px 0 0 6px;
        }
        .event-link:hover { background-color: #f8f9fa; text-decoration: none; }
        /* * END NEW CSS * */

        .event-title-text { font-weight: 500; word-break: break-word; }
        .event-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; padding: 10px 15px; }
        .event-actions .btn { padding: 4px 8px; font-size: 12px; height: auto; gap: 4px; }
        .event-actions .btn i { font-size: 0.9em; }
        .no-events { text-align: center; color: #777; padding: 20px; font-style: italic; font-size: 15px; background-color: #f9f9f9; border: 1px dashed #ddd; border-radius: 6px; }
        /* Add class for search error messages */
        .error-message { color: #dc3545; background-color: #f8d7da; border-color: #f5c6cb; } /* Kept this but likely unused now */
        /* --- Create/Edit Event Modal Styling --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%; /* Full viewport height */
            background-color: rgba(0,0,0,0.5);
            align-items: flex-start; /* Align modal box to top */
            justify-content: center; /* Keep horizontal centering */
            /* overflow-y: auto; / / REMOVED: Don't scroll the background */
            padding-top: 50px; /* ADDED/ADJUSTED: Space from top edge (adjust value as needed) */
            padding-bottom: 50px; /* ADDED/ADJUSTED: Space from bottom edge (adjust value as needed) */
            padding-left: 15px; /* Optional side padding */
            padding-right: 15px; /* Optional side padding */
        }

        .modal-content {
            background-color: #fff;
            padding: 25px 35px 10px 35px;
            border-radius: 8px;
            width: 90%;
            max-width: 550px; /* Kept your increased width */
            position: relative;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            margin: 0 auto; /* Changed from '20px auto' - let padding on .modal handle vertical space */
            /* --- ADD THESE --- */
            max-height: calc(100vh - 100px); /* Max height = Viewport height - (padding-top + padding-bottom from .modal) */
            overflow-y: auto; /* Add scrollbar INSIDE this white box if content overflows */
            /* --- END ADD --- */
        }

        .modal-content h2 {
            font-size: 20px;
            margin-bottom: 25px;
            color: #333;
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            font-weight: 600;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 26px;
            cursor: pointer;
            color: #aaa;
            line-height: 1;
            z-index: 10;
        }
        .close:hover { color: #333; }

        .form-field { margin-bottom: 18px; }

        .form-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

/* ... rest of your styles for inputs, selects, etc. ... */
        /* Apply to select as well */
        .form-field input[type="text"], .form-field input[type="date"], .form-field textarea, .form-field select {
            width: 100%; padding: 10px 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px;
             height: 38px; /* Consistent height / line-height: 1.5; / Adjust line height */
        }
        .form-field select { padding-right: 30px; background-position: right 0.75rem center; background-repeat: no-repeat; background-size: 16px 12px; } /* Basic dropdown arrow space */
        .form-field textarea { resize: vertical; min-height: 90px; height: auto; }
        .form-field button[type="submit"] { width: auto; font-weight: 600; }
        #eventForm .form-field:last-of-type { text-align: center; margin-top: 10px; }
        .form-field button[type="submit"]:disabled { background-color: #cccccc; cursor: not-allowed; opacity: 0.7; }
        /* Updated error message selector */
        .form-field span.error-message { color: #dc3545; font-size: 0.85em; margin-top: 5px; display: block; min-height: 1em; }
        /* Updated invalid input selector */
        .form-field input.is-invalid, .form-field textarea.is-invalid, .form-field select.is-invalid { border-color: #dc3545; }
        .form-text { font-size: 0.8em; color: #6c757d; margin-top: 4px; }

    </style>
@endpush

{{-- Define the main content section for the layout --}}
@section('content')
<div id="manage-view">
    <div class="container"> {{-- Assuming .container class exists in admin.css --}}
        <header>
            <h1>Manage Events</h1>
        </header>

        {{-- Search and Top Actions Area --}}
        <div class="search-actions-container">
            {{-- Search elements as before --}}
            <div class="search-input-group">
                <input type="text" id="searchBar" class="search-bar" placeholder="Search by Title, Location, Date (YYYY-MM-DD)...">
                <button type="button" class="search-icon" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
            {{-- Top buttons as before --}}
            <div class="top-level-actions">
                <button type="button" class="btn btn-primary" id="createEventBtn">
                    <i class="fas fa-plus"></i> Create Event
                </button>
                <a href="{{ route('admin.events.history_page') }}" class="btn btn-secondary">
                    <i class="fas fa-history"></i>
                    <span>View History</span>
                </a>
            </div>
        </div>

        {{-- Event List --}}
        <div class="event-list">
            <ul id="event-list">
                <li class="no-events">Loading events...</li>
            </ul>
        </div>

    </div> {{-- End .container --}}
</div> {{-- End #manage-view --}}

{{-- Create/Edit Event Modal --}}
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEventModalBtn" title="Close">×</span>
        <h2 id="modalTitle">Create Event</h2>
        {{-- The form action and method are set dynamically by JavaScript --}}
        <form id="eventForm" method="POST" novalidate>
             @csrf
             <input type="hidden" id="editEventId" name="edit_id">
             <input type="hidden" name="_method" id="modalMethodField" value="POST">

             {{-- Existing Fields --}}
             <div class="form-field">
                 <label for="event-title">Event Title *</label>
                 <input type="text" id="event-title" name="title" required>
                 <span class="error-message" data-field="title"></span>
            </div>
             <div class="form-field">
                 <label for="event-description">Event Description</label>
                 <textarea id="event-description" name="description"></textarea>
                 <span class="error-message" data-field="description"></span>
            </div>
             <div class="form-field">
                 <label for="event-date">Event Date</label>
                 <input type="date" id="event-date" name="event_date">
                 <span class="error-message" data-field="event_date"></span>
            </div>
             <div class="form-field">
                 <label for="event-location">Event Location</label>
                 <input type="text" id="event-location" name="location">
                 <span class="error-message" data-field="location"></span>
             </div>

             {{-- ========================================== --}}
             {{-- == START: NEW FORM FIELDS ADDED HERE == --}}
             {{-- Inserted after Location, before Save button --}}
             {{-- ========================================== --}}

             {{-- Type Selection Dropdown --}}
             <div class="form-field">
                 <label for="voterType">Voter Type*:</label>
                 {{-- Use existing styling or add new class like 'form-select' if defined in admin.css --}}
                 <select name="type" id="voterType" required>
                     <option value="" disabled selected>-- Select Type --</option>
                     <option value="all">All (Students & Staff)</option>
                     <option value="students">Students Only</option>
                     <option value="staff">Staff Only</option>
                 </select>
                 <div class="form-text">Who is generally eligible? Required.</div>
                 <span class="error-message" data-field="type"></span>
             </div>

             {{-- Gender Restriction Dropdown --}}
             <div class="form-field">
                 <label for="eventGenderRestriction">Gender:</label>
                 <select name="gender_restriction" id="eventGenderRestriction" required>
                     <option value="both">Both Genders</option>
                     <option value="female">Female Only</option>
                     <option value="female">Male Only</option>
                 </select>
                 <div class="form-text">Restrict voting based on gender? Required.</div>
                 <span class="error-message" data-field="gender_restriction"></span>
             </div>

             {{-- Programme Selection Dropdown --}}
             <div class="form-field">
                 <label for="eventProgramme">Programme From:</label>
                 <select name="programme_id" id="eventProgramme">
                     <option value="">-- All Programmes --</option>
                     {{-- Use the $programmes variable passed from EventController@index --}}
                     @isset($programmes) {{-- Check if $programmes exists --}}
                         @forelse($programmes as $programme)
                             <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                         @empty
                             <option value="" disabled>No programmes defined</option>
                         @endforelse
                     @else {{-- Handle case where $programmes not passed --}}
                         <option value="" disabled>Programme data unavailable</option>
                     @endisset
                 </select>
                 <div class="form-text">Optional. Restrict voting only to voters in this programme.</div>
                 <span class="error-message" data-field="programme_id"></span>
             </div>
             {{-- Existing Save Button --}}
             <div class="form-field">
                 <button type="submit" id="saveEventBtn" class="btn btn-primary">Save Event</button>
             </div>
        </form>
    </div>
</div>
@endsection

{{-- Push page-specific scripts into the layout's 'scripts' stack --}}
@push('scripts')
<script>
    // --- JAVASCRIPT FOR MANAGE EVENTS PAGE ---
    document.addEventListener('DOMContentLoaded', function() {

        // --- Element References ---
        const eventModal = document.getElementById('eventModal');
        const eventForm = document.getElementById('eventForm');
        const createEventBtn = document.getElementById('createEventBtn');
        const closeEventModalBtn = document.getElementById('closeEventModalBtn');
        const eventListUl = document.getElementById('event-list');
        const editEventIdInput = document.getElementById('editEventId');
        const modalMethodField = document.getElementById('modalMethodField');
        const modalTitleH2 = document.getElementById('modalTitle');
        const saveEventBtn = document.getElementById('saveEventBtn');
        // Input field references (original)
        const eventTitleInput = document.getElementById('event-title');
        const eventDescriptionTextarea = document.getElementById('event-description');
        const eventDateInput = document.getElementById('event-date');
        const eventLocationInput = document.getElementById('event-location');
        // --- Get references to NEW select elements ---
        const eventTypeSelect = document.getElementById('eventType');
        const eventGenderSelect = document.getElementById('eventGenderRestriction');
        const eventProgrammeSelect = document.getElementById('eventProgramme');
        // --- END ---
        // Search references
        const searchBar = document.getElementById('searchBar');
        const searchBtn = document.getElementById('searchBtn');

        // --- CSRF Token ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // --- API URLs ---
        const urls = {
            store: "{{ route('admin.events.store') }}",
            destroyBase: "{{ route('admin.events.destroy', ':id') }}",
            editBase: "{{ route('admin.events.edit', ':id') }}",
            updateBase: "{{ route('admin.events.update', ':id') }}",
            search: "{{ route('admin.events.search') }}"
        };

        // --- Helper Functions --- (Keep exactly as you provided)
        function escapeHTML(str) { const div = document.createElement('div'); div.appendChild(document.createTextNode(str || '')); return div.innerHTML; }
        function clearFormErrors() { eventForm?.querySelectorAll('.error-message').forEach(span => span.textContent = ''); eventForm?.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid')); }
        function displayFormErrors(errors) { clearFormErrors(); for (const field in errors) { const inputField = eventForm?.querySelector(`[name="${field}"]`); const errorSpan = eventForm?.querySelector(`.error-message[data-field="${field}"]`); if (inputField) inputField.classList.add('is-invalid'); if (errorSpan) errorSpan.textContent = errors[field][0]; } }
        function renderEventListItem(event) { const title = escapeHTML(event.title || 'Untitled Event'); const id = event.id; if (!id) return ''; const scanUrl = "{{ route('admin.events.scan_page', ':id') }}".replace(':id', id); return `<li data-id="${id}" data-title="${title}"><a href="${scanUrl}" class="event-link" title="Scan voters for ${title}"><span class="event-title-text">${title}</span></a><div class="event-actions"><button type="button" class="btn btn-edit edit-btn" data-id="${id}" title="Edit Event"><i class="fas fa-edit"></i> Edit</button><button type="button" class="btn btn-delete delete-btn" data-id="${id}" title="Delete Event"><i class="fas fa-trash-alt"></i> Delete</button></div></li>`; }
        function displayEventList(events) { if (!eventListUl) return; eventListUl.innerHTML = ''; if (!Array.isArray(events) || events.length === 0) { const currentQuery = searchBar ? searchBar.value.trim() : ''; if (currentQuery === '') { eventListUl.innerHTML = '<li class="no-events">No active events found.</li>'; } else { eventListUl.innerHTML = '<li class="no-events">No events found matching your search.</li>'; } } else { let listHtml = ''; events.forEach(event => { listHtml += renderEventListItem(event); }); eventListUl.innerHTML = listHtml; } }
        let searchTimeout; async function performSearch() { if (!eventListUl || !urls.search) { console.error("Cannot perform search: Missing list element or search URL."); return; } const query = searchBar ? searchBar.value.trim() : ''; eventListUl.innerHTML = '<li class="no-events">Loading...</li>'; const searchUrl = new URL(urls.search, window.location.origin); searchUrl.searchParams.append('query', query); try { const response = await fetch(searchUrl.toString(), { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', } }); if (!response.ok) { throw new Error(`Search request failed: ${response.statusText || response.status}`); } const data = await response.json(); displayEventList(data.events || []); } catch (error) { console.error('Error performing search:', error); eventListUl.innerHTML = `<li class="no-events error-message">Could not load events: ${error.message}. Please try again.</li>`; } }


        // --- Modal Handling Functions ---
        function openModalForCreate() { /* Keep exactly as you provided */
             eventForm?.reset(); clearFormErrors(); editEventIdInput.value = ''; modalMethodField.value = 'POST';
             modalTitleH2.textContent = 'Create Event'; saveEventBtn.textContent = 'Save Event'; saveEventBtn.disabled = false;
             eventForm.action = urls.store; eventModal.style.display = 'flex'; eventTitleInput?.focus();
         }

        // **** MODIFIED openModalForEdit ****
        function openModalForEdit(eventId) {
             if (!eventId || !urls.editBase) { alert('Error: Cannot edit event. Config/ID missing.'); return; }
             const url = urls.editBase.replace(':id', eventId);
             eventForm?.reset(); clearFormErrors();
             modalTitleH2.textContent = 'Loading Event...'; saveEventBtn.textContent = 'Loading...'; saveEventBtn.disabled = true;
             eventModal.style.display = 'flex';

             fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
             .then(response => { if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`); return response.json(); })
             .then(data => {
                 const event = data.event; if (!event) throw new Error('Event data not found.');
                 // Populate standard fields
                 eventTitleInput.value = event.title || '';
                 eventDescriptionTextarea.value = event.description || '';
                 eventDateInput.value = event.event_date ? event.event_date.split('T')[0] : '';
                 eventLocationInput.value = event.location || '';

                 // --- Populate NEW Dropdowns ---
                 // Ensure the select elements exist before setting value
                 if(eventTypeSelect) eventTypeSelect.value = event.type || 'all';
                 if(eventGenderSelect) eventGenderSelect.value = event.gender_restriction || 'both';
                 if(eventProgrammeSelect) eventProgrammeSelect.value = event.programme_id || ''; // Use '' for null
                 // --- END ---

                 editEventIdInput.value = event.id; modalMethodField.value = 'PUT';
                 modalTitleH2.textContent = 'Edit Event'; saveEventBtn.textContent = 'Update Event';
                 saveEventBtn.disabled = false; eventForm.action = urls.updateBase.replace(':id', event.id);
                 eventTitleInput?.focus();
             })
             .catch(error => { console.error('Error fetching event details:', error); alert(`Could not load event details: ${error.message}`); closeModal(); });
         }
        // **** END MODIFIED openModalForEdit ****

        function closeModal() { if(eventModal) eventModal.style.display = 'none'; } /* Keep exactly as you provided */

        // --- Form Submission (Create/Update via Modal) ---
        // Keep exactly as you provided - FormData handles new fields
        eventForm?.addEventListener('submit', async function(event) {
             event.preventDefault(); const formData = new FormData(eventForm); const currentId = editEventIdInput.value;
             let url = eventForm.action; let method = 'POST';
             if (currentId && modalMethodField.value === 'PUT') { formData.append('_method', 'PUT'); }
             saveEventBtn.disabled = true; saveEventBtn.textContent = currentId ? 'Updating...' : 'Saving...'; clearFormErrors();
             if (!url) { alert("Configuration error: Form URL missing."); saveEventBtn.disabled = false; saveEventBtn.textContent = currentId ? 'Update Event' : 'Save Event'; return; }
             try {
                 const response = await fetch(url, { method: method, headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', }, body: formData });
                 const result = await response.json();
                 if (!response.ok) { if (response.status === 422 && result.errors) { displayFormErrors(result.errors); } else { alert(result.message || `An error occurred (Status: ${response.status}).`); console.error("Error Response:", result); } throw new Error('Submission failed'); }
                 alert(result.message || 'Operation successful!'); closeModal(); await performSearch();
             } catch (error) { console.error('Fetch/Submission Error:', error); if (error.message !== 'Submission failed') { alert('A network or processing error occurred.'); }
             } finally { saveEventBtn.disabled = false; saveEventBtn.textContent = editEventIdInput.value ? 'Update Event' : 'Save Event'; }
         });

        // --- Delete Event Logic ---
         async function deleteEvent(eventId, listItemElement) { /* Keep exactly as you provided */
            const eventTitle = listItemElement?.dataset.title || 'this event'; if (!urls.destroyBase || !eventId) { alert('Configuration error.'); return; } if (!confirm(`Are you sure you want to delete "${eventTitle}" and its associated vote records? This cannot be undone.`)) { return; } const url = urls.destroyBase.replace(':id', eventId); try { const response = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'} }); if (response.ok) { let result = { message: 'Event deleted successfully.' }; if (response.status !== 204) { result = await response.json(); } alert(result.message || 'Operation successful!'); await performSearch(); } else { let errorMsg = `Request failed with status ${response.status}`; try { const errData = await response.json(); errorMsg = errData.message || errorMsg; } catch(e) { /* ignore */ } throw new Error(errorMsg); } } catch (error) { console.error('Error deleting event:', error); alert(`Could not delete event: ${error.message}`); }
          }

        // --- Event Listeners Setup --- (Keep exactly as you provided)
        createEventBtn?.addEventListener('click', openModalForCreate);
        closeEventModalBtn?.addEventListener('click', closeModal);
        eventModal?.addEventListener('click', (e) => { if (e.target === eventModal) closeModal(); });
        eventListUl?.addEventListener('click', (e) => { const editBtn = e.target.closest('.edit-btn'); const deleteBtn = e.target.closest('.delete-btn'); const listItem = e.target.closest('li'); if (editBtn && listItem) { e.preventDefault(); openModalForEdit(editBtn.dataset.id); } else if (deleteBtn && listItem) { e.preventDefault(); deleteEvent(deleteBtn.dataset.id, listItem); } });
        searchBar?.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 400); });
        searchBtn?.addEventListener('click', () => { clearTimeout(searchTimeout); performSearch(); });

        // --- Initial Load --- (Keep exactly as you provided)
        performSearch(); // Load initial event list

    }); // End DOMContentLoaded
</script>
@endpush