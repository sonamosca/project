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
            /* ** REMOVE padding from li, it's now on the link/actions div ** */
            padding: 0;
        }

        /* ** NEW CSS for the link area ** */
        .event-link {
            display: block; /* Make it block-level */
            flex-grow: 1;   /* Allow it to take available space */
            padding: 10px 15px; /* Add padding inside the link */
            text-decoration: none; /* Remove underline */
            color: inherit; /* Inherit text color (usually black/dark grey) */
            cursor: pointer;
            border-radius: 6px 0 0 6px; /* Match li radius on the left */
        }
        .event-link:hover {
            background-color: #f8f9fa; /* Subtle hover effect */
            text-decoration: none; /* Ensure no underline on hover */
        }
        /* ** END NEW CSS ** */

        .event-title-text {
            /* flex-grow: 1; */ /* No longer needed here */
            font-weight: 500;
            /* margin-right: 15px; */ /* No longer needed here */
            word-break: break-word;
        }
        .event-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            /* ** ADD padding to match link padding ** */
            padding: 10px 15px;
        }
        /* Smaller buttons for list items */
        .event-actions .btn {
            padding: 4px 8px;
            font-size: 12px;
            height: auto;
            gap: 4px;
        }
        .event-actions .btn i { font-size: 0.9em; }


        .no-events { text-align: center; color: #777; padding: 20px; font-style: italic; font-size: 15px; background-color: #f9f9f9; border: 1px dashed #ddd; border-radius: 6px; }

        /* --- Create/Edit Event Modal Styling --- */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 25px 35px; border-radius: 8px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25); }
        .modal-content h2 { font-size: 20px; margin-bottom: 25px; color: #333; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 15px; font-weight: 600;}
        .close { position: absolute; top: 10px; right: 15px; font-size: 26px; cursor: pointer; color: #aaa; line-height: 1; z-index: 10; }
        .close:hover { color: #333; }
        .form-field { margin-bottom: 18px; }
        .form-field label { display: block; margin-bottom: 6px; font-weight: 600; color: #555; font-size: 14px; }
        .form-field input[type="text"], .form-field input[type="date"], .form-field textarea { width: 100%; padding: 10px 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .form-field textarea { resize: vertical; min-height: 90px; }
        .form-field button[type="submit"] { width: auto; font-weight: 600; }
        #eventForm .form-field:last-of-type { text-align: center; margin-top: 10px; }
        .form-field button[type="submit"]:disabled { background-color: #cccccc; cursor: not-allowed; opacity: 0.7; }
        .form-field .error-message { color: #dc3545; font-size: 0.85em; margin-top: 5px; display: block; min-height: 1em; }
        .form-field input.is-invalid, .form-field textarea.is-invalid { border-color: #dc3545; }

    </style>
@endpush

{{-- Define the main content section for the layout --}}
@section('content')
<div id="manage-view">
    <div class="container">
        <header>
            <h1>Manage Events</h1>
        </header>

        {{-- Search and Top Actions Area --}}
        <div class="search-actions-container">
            <div class="search-input-group">
                <input type="text" id="searchBar" class="search-bar" placeholder="Search events...">
                <button type="button" class="search-icon" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
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
                {{-- Events loaded via Blade loop initially - HTML updated below --}}
                @forelse ($events ?? [] as $event)
                    <li data-id="{{ $event->id }}" data-title="{{ $event->title }}">
                        {{-- Link wraps the title area --}}
                        <a href="{{ route('admin.events.scan_page', $event->id) }}" class="event-link" title="Scan voters for {{ $event->title }}">
                            <span class="event-title-text">{{ $event->title }}</span>
                        </a>
                        {{-- Actions remain separate --}}
                        <div class="event-actions">
                            <button type="button" class="btn btn-edit edit-btn" data-id="{{ $event->id }}" title="Edit Event">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-delete delete-btn" data-id="{{ $event->id }}" title="Delete Event">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                    </li>
                @empty
                    <li class="no-events">No active events found.</li>
                @endforelse
            </ul>
        </div>

    </div> {{-- End .container --}}
</div> {{-- End #manage-view --}}

{{-- Create/Edit Event Modal (Remains the same) --}}
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEventModalBtn" title="Close">×</span>
        <h2 id="modalTitle">Create Event</h2>
        <form id="eventForm" action="{{ route('admin.events.store') }}" method="POST" novalidate>
             @csrf
             <input type="hidden" id="editEventId" name="edit_id">
             <input type="hidden" name="_method" id="modalMethodField" value="POST">

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
        const eventTitleInput = document.getElementById('event-title');
        const eventDescriptionTextarea = document.getElementById('event-description');
        const eventDateInput = document.getElementById('event-date');
        const eventLocationInput = document.getElementById('event-location');
        const searchBar = document.getElementById('searchBar'); // Add search bar ref
        const searchBtn = document.getElementById('searchBtn');   // Add search button ref

        // --- CSRF Token ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // --- API URLs ---
        const urls = {
            store: "{{ route('admin.events.store') }}",
            destroyBase: "{{ route('admin.events.destroy', ':id') }}",
            editBase: "{{ route('admin.events.edit', ':id') }}",
            updateBase: "{{ route('admin.events.update', ':id') }}",
        };

        // --- Helper Functions ---
        function escapeHTML(str) { /* ... (keep existing) ... */
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str || ''));
            return div.innerHTML;
         }
        function clearFormErrors() { /* ... (keep existing) ... */
            eventForm?.querySelectorAll('.error-message').forEach(span => span.textContent = '');
            eventForm?.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
         }
        function displayFormErrors(errors) { /* ... (keep existing) ... */
             clearFormErrors();
             for (const field in errors) {
                 const inputField = eventForm?.querySelector(`[name="${field}"]`);
                 const errorSpan = eventForm?.querySelector(`.error-message[data-field="${field}"]`);
                 if (inputField) inputField.classList.add('is-invalid');
                 if (errorSpan) errorSpan.textContent = errors[field][0];
             }
         }

        // ** UPDATED: Renders a single list item's HTML with link **
        function renderEventListItem(event) {
             const title = escapeHTML(event.title || 'Untitled Event');
             const id = event.id;
             if (!id) return '';
             // Generate the URL for the scan page
             const scanUrl = "{{ route('admin.events.scan_page', ':id') }}".replace(':id', id);
             return `
                 <li data-id="${id}" data-title="${title}">
                     <a href="${scanUrl}" class="event-link" title="Scan voters for ${title}">
                         <span class="event-title-text">${title}</span>
                     </a>
                     <div class="event-actions">
                         <button type="button" class="btn btn-edit edit-btn" data-id="${id}" title="Edit Event">
                             <i class="fas fa-edit"></i> Edit
                         </button>
                         <button type="button" class="btn btn-delete delete-btn" data-id="${id}" title="Delete Event">
                             <i class="fas fa-trash-alt"></i> Delete
                         </button>
                     </div>
                 </li>`;
         }

        // Adds a newly created event to the top of the list
        function addEventToList(eventData) { /* ... (keep existing - uses updated renderEventListItem) ... */
             if (!eventData || !eventData.id || !eventListUl) return;
             const newLiHtml = renderEventListItem(eventData);
             const noEventsLi = eventListUl.querySelector('.no-events');
             if (noEventsLi) {
                 noEventsLi.remove();
             }
             eventListUl.insertAdjacentHTML('afterbegin', newLiHtml);
         }

        // Updates an existing event in the list
        function updateEventInList(eventData) { /* ... (keep existing - uses updated renderEventListItem) ... */
             if (!eventData || !eventData.id || !eventListUl) return;
             const listItem = eventListUl.querySelector(`li[data-id="${eventData.id}"]`);
             if (listItem) {
                 listItem.outerHTML = renderEventListItem(eventData); // Re-render with potentially new title/scan URL
             } else {
                 console.warn(`List item with ID ${eventData.id} not found for update. Adding instead.`);
                 addEventToList(eventData);
             }
         }

        // --- Modal Handling Functions ---
        function openModalForCreate() { /* ... (keep existing) ... */
             eventForm?.reset();
             clearFormErrors();
             editEventIdInput.value = '';
             modalMethodField.value = 'POST';
             modalTitleH2.textContent = 'Create Event';
             saveEventBtn.textContent = 'Save Event';
             saveEventBtn.disabled = false;
             eventForm.action = urls.store;
             eventModal.style.display = 'flex';
             eventTitleInput?.focus();
         }
        function openModalForEdit(eventId) { /* ... (keep existing) ... */
             if (!eventId || !urls.editBase) { alert('Error: Cannot edit event. Configuration or ID missing.'); return; }
             const url = urls.editBase.replace(':id', eventId);
             eventForm?.reset(); clearFormErrors();
             modalTitleH2.textContent = 'Loading Event...';
             saveEventBtn.textContent = 'Loading...'; saveEventBtn.disabled = true;
             eventModal.style.display = 'flex';
             fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
             .then(response => { if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`); return response.json(); })
             .then(data => {
                 const event = data.event; if (!event) throw new Error('Event data not found in response.');
                 eventTitleInput.value = event.title || '';
                 eventDescriptionTextarea.value = event.description || '';
                 eventDateInput.value = event.event_date ? event.event_date.split('T')[0] : '';
                 eventLocationInput.value = event.location || '';
                 editEventIdInput.value = event.id; modalMethodField.value = 'PUT';
                 modalTitleH2.textContent = 'Edit Event'; saveEventBtn.textContent = 'Update Event';
                 saveEventBtn.disabled = false; eventForm.action = urls.updateBase.replace(':id', event.id);
                 eventTitleInput?.focus();
             })
             .catch(error => { console.error('Error fetching event details:', error); alert(`Could not load event details: ${error.message}`); closeModal(); });
         }
        function closeModal() { /* ... (keep existing) ... */
            if(eventModal) eventModal.style.display = 'none';
         }

        // --- Form Submission (Create/Update via Modal) ---
        eventForm?.addEventListener('submit', function(event) { /* ... (keep existing) ... */
             event.preventDefault(); const formData = new FormData(eventForm); const currentId = editEventIdInput.value;
             let url = eventForm.action; let method = 'POST';
             if (currentId) { formData.append('_method', modalMethodField.value); }
             saveEventBtn.disabled = true; saveEventBtn.textContent = currentId ? 'Updating...' : 'Saving...'; clearFormErrors();
             if (!url) { alert("Configuration error: Form URL missing."); saveEventBtn.disabled = false; saveEventBtn.textContent = currentId ? 'Update Event' : 'Save Event'; return; }
             fetch(url, { method: method, headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', }, body: formData })
             .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, body: data })))
             .then(result => {
                 if (!result.ok) {
                     if (result.status === 422 && result.body.errors) { displayFormErrors(result.body.errors); }
                     else { alert(result.body.message || `An error occurred (Status: ${result.status}).`); console.error("Error Response:", result.body); }
                     throw new Error('Submission failed');
                 }
                 alert(result.body.message || 'Operation successful!'); closeModal();
                 if (result.body.event) { if (currentId) { updateEventInList(result.body.event); } else { addEventToList(result.body.event); } }
                 else { console.warn("Response did not include event data."); }
             })
             .catch(error => { console.error('Fetch/Submission Error:', error); if (error.message !== 'Submission failed') { alert('A network or processing error occurred.'); } })
             .finally(() => { saveEventBtn.disabled = false; saveEventBtn.textContent = editEventIdInput.value ? 'Update Event' : 'Save Event'; });
         });

        // --- Delete Event Logic ---
        function deleteEvent(eventId, listItemElement) { /* ... (keep existing - uses updated confirmation message from controller changes) ... */
             const eventTitle = listItemElement?.dataset.title || 'this event';
             if (!urls.destroyBase || !eventId) { alert('Configuration error.'); return; }
             // Updated confirmation message might be good here
             if (!confirm(`Are you sure you want to delete "${eventTitle}" and its associated vote records? This cannot be undone.`)) { return; }
             const url = urls.destroyBase.replace(':id', eventId);
             fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', } })
             .then(response => {
                 if (!response.ok) { return response.json().then(errData => { throw new Error(errData.message || `Failed (Status: ${response.status})`); }).catch(() => { throw new Error(`Failed (Status: ${response.status})`); }); }
                 if (response.status === 204) { return { message: 'Event deleted successfully.' }; } return response.json();
             })
             .then(result => { alert(result.message || 'Operation successful!'); listItemElement.remove(); if (eventListUl && eventListUl.children.length === 0) { eventListUl.innerHTML = '<li class="no-events">No active events found.</li>'; } })
             .catch(error => { console.error('Error deleting event:', error); alert(`Could not delete event: ${error.message}`); });
         }

        // --- Event Listeners Setup ---
        createEventBtn?.addEventListener('click', openModalForCreate);
        closeEventModalBtn?.addEventListener('click', closeModal);
        eventModal?.addEventListener('click', (e) => { if (e.target === eventModal) closeModal(); });

        // ** UPDATED: Event listener for list clicks **
        eventListUl?.addEventListener('click', (e) => {
            // Find the closest relevant element that was clicked
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');
            const listItem = e.target.closest('li'); // Get the parent li regardless

            // If the Edit button (or its icon) was clicked
            if (editBtn && listItem) {
                e.preventDefault(); // *** IMPORTANT: Prevent the link from navigating ***
                openModalForEdit(editBtn.dataset.id);
            }
            // Else if the Delete button (or its icon) was clicked
            else if (deleteBtn && listItem) {
                e.preventDefault(); // *** IMPORTANT: Prevent the link from navigating ***
                deleteEvent(deleteBtn.dataset.id, listItem);
            }
            // Otherwise, if the click was on the link area (.event-link) or the title span,
            // do nothing here. The browser will handle the navigation via the <a> tag's href.
        });

        // --- Search/Filter Logic (Example) ---
        function filterEvents() { /* ... (keep existing or implement as needed) ... */
             const searchTerm = searchBar.value.toLowerCase().trim();
             const listItems = eventListUl?.querySelectorAll('li[data-id]');
             let found = false;
             listItems?.forEach(item => {
                 const title = item.dataset.title?.toLowerCase() || '';
                 const isVisible = title.includes(searchTerm);
                 item.style.display = isVisible ? 'flex' : 'none'; // Use 'flex' due to li styling
                 if (isVisible) found = true;
             });
             const noEventsLi = eventListUl?.querySelector('.no-events');
             if (noEventsLi) noEventsLi.style.display = 'none'; // Hide initial message
             // Optional: Add a 'no search results' message if !found && searchTerm
         }
        searchBtn?.addEventListener('click', filterEvents);
        searchBar?.addEventListener('keyup', (e) => { if (e.key === 'Enter') { filterEvents(); } });
        searchBar?.addEventListener('input', () => { if (searchBar.value === '') { filterEvents(); } }); // Filter when cleared


    }); // End DOMContentLoaded
</script>
@endpush