{{-- Use a layout file if you have one, e.g., layouts.admin --}}
{{-- @extends('layouts.admin') --}}

{{-- @section('title', 'Manage Event Category') --}}

{{-- @push('styles') --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* --- Base Styles --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f8; color: #2c3e50; }

        /* --- View Containers --- */
        #manage-view, #scan-view {
            max-width: 1100px; margin: 30px auto; padding: 30px 40px; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        #scan-view { display: none; max-width: 1000px; }

        /* --- Manage View Specific Styles --- */
        #manage-view header h1 { font-size: 32px; margin-bottom: 30px; text-align: center; color: #1a1a1a; }
        .search-container { display: flex; justify-content: center; align-items: center; margin-bottom: 40px; gap: 10px; position: relative; }
        .search-bar { width: 100%; max-width: 500px; padding: 12px 18px; font-size: 16px; border: 1px solid #ccc; border-radius: 6px 0 0 6px; }
        .search-icon { padding: 12px 18px; border: 1px solid #ccc; border-left: none; background-color: #0077b6; color: #fff; cursor: pointer; border-radius: 0 6px 6px 0; transition: background-color 0.3s; }
        .dropdown { position: relative; }
        .dropdown-btn { background: none; border: none; font-size: 20px; cursor: pointer; padding: 12px; }
        .dropdown-menu { position: absolute; top: 50px; right: 0; background-color: white; border: 1px solid #ccc; border-radius: 6px; display: none; flex-direction: column; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); z-index: 999; min-width: 150px; }
        .dropdown-menu button { padding: 10px 20px; border: none; background: none; cursor: pointer; text-align: left; font-size: 16px; width: 100%; display: flex; align-items: center; gap: 10px; }
        .dropdown-menu button i { width: 20px; text-align: center; }
        .dropdown-menu button:hover { background-color: #f0f0f0; }
        .event-list h2, .event-history h2 { font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; }
        #event-list, #history-list { list-style: none; padding: 0; }
        #event-list li { margin-bottom: 12px; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 18px; position: relative; }
        #history-list li { padding: 15px; margin-bottom: 12px; background-color: #f0f0f0; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 16px; } /* History item style */
        .event-title-container { display: flex; justify-content: space-between; align-items: center; padding: 15px; }
        .event-title-text { flex-grow: 1; margin-right: 10px; font-weight: 600; }
        .event-options { position: relative; cursor: pointer; margin-left: auto; }
        .event-options i.options-icon { font-size: 18px; padding: 5px; color: #555; }
        .event-dropdown { display: none; position: absolute; right: 0; top: 30px; background: white; border: 1px solid #ccc; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 999; min-width: 120px; }
        .event-dropdown button { display: flex; align-items: center; gap: 8px; width: 100%; padding: 10px 15px; border: none; background: none; text-align: left; font-size: 14px; cursor: pointer; }
        .edit-btn i { color: #007bff; }
        .delete-btn i { color: #dc3545; }
        .no-events, .no-history { text-align: center; color: #777; padding: 20px; font-style: italic; }

        /* --- Create/Edit Event Modal Styling --- */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 30px 40px; border-radius: 8px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25); }
        .modal-content h2 { font-size: 24px; margin-bottom: 25px; color: #333; text-align: center; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .close { position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: #aaa; z-index: 10; }
        .form-field { margin-bottom: 20px; }
        .form-field label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-field input[type="text"], .form-field input[type="date"], .form-field textarea { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; }
        .form-field textarea { resize: vertical; min-height: 100px; }
        .form-field button { padding: 12px 20px; background-color: #0077b6; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%; font-weight: 600; }
        .form-field button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .form-field .error-message { color: #dc3545; font-size: 0.875em; margin-top: 4px; display: block; height: 1em; }
        .form-field input.is-invalid, .form-field textarea.is-invalid { border-color: #dc3545; }
        /* --- Animation Keyframes (Optional) --- */
        /* @keyframes fadeIn { ... } @keyframes slideIn { ... } */
    </style>
{{-- @endpush --}}

{{-- @section('content') --}}
<div id="manage-view">
    <div class="container">
        <header>
            <h1>Manage Event Category</h1>
        </header>

        <div class="search-container">
            <input type="text" id="searchBar" class="search-bar" placeholder="Search events...">
            <button type="button" class="search-icon" id="searchBtn"><i class="fas fa-search"></i></button>
            <div class="dropdown">
                <button type="button" class="dropdown-btn" id="menuBtn"><i class="fas fa-ellipsis-v"></i></button>
                <div class="dropdown-menu" id="menuDropdown">
                    <button type="button" id="createEventBtn"><i class="fas fa-plus"></i> Create Category</button>
                    <button type="button" id="toggleHistoryBtn"><i class="fas fa-history"></i> View History</button>
                </div>
            </div>
        </div>

        <div class="event-list">
            <h2>Available Event Categories</h2>
            {{-- In admin.manage_event.blade.php --}}
<ul id="event-list">
    {{-- Loop through $events passed from controller --}}
    @forelse ($events ?? [] as $event)
        <li data-id="{{ $event->id }}" data-title="{{ $event->title }}">
            <div class="event-title-container">
                <span class="event-title-text">{{ $event->title }}</span>
                <div class="event-options">
                    <i class="fas fa-ellipsis-v options-icon"></i>
                    <div class="event-dropdown" style="display: none;">
                        <button type="button" class="edit-btn" data-id="{{ $event->id }}"><i class="fas fa-edit"></i> Edit</button>
                        <button type="button" class="delete-btn" data-id="{{ $event->id }}"><i class="fas fa-trash-alt"></i> Delete</button>
                    </div>
                </div>
            </div>
        </li>
    @empty
        {{-- This is shown IF $events is empty --}}
        <li class="no-events">No active event categories found.</li>
    @endforelse
</ul>
        </div>

        <div class="event-history" id="eventHistory" style="display: none;">
            <h2>Event History</h2>
            <ul id="history-list">
                <li class="no-history">Loading history...</li>
            </ul>
        </div>
    </div>
</div>

{{-- Create/Edit Event Modal --}}
<div id="eventModal" class="modal">
   <div class="modal-content">
        <span class="close" id="closeEventModalBtn">×</span>
        <h2 id="modalTitle">Create Event Category</h2>
        <form id="eventForm" action="{{ route('event-categories.store') }}" method="POST" novalidate>
             @csrf
             <input type="hidden" id="editEventId" name="edit_id">
             <input type="hidden" name="_method" id="modalMethodField" value="POST">
             <div class="form-field"> <label for="event-title">Event Title *</label> <input type="text" id="event-title" name="title" required> <span class="error-message" data-field="title"></span> </div>
             <div class="form-field"> <label for="event-description">Event Description</label> <textarea id="event-description" name="description"></textarea> <span class="error-message" data-field="description"></span> </div>
             <div class="form-field"> <label for="event-date">Event Date</label> <input type="date" id="event-date" name="event_date"> <span class="error-message" data-field="event_date"></span> </div>
             <div class="form-field"> <label for="event-location">Event Location</label> <input type="text" id="event-location" name="location"> <span class="error-message" data-field="location"></span> </div>
             {{-- ** Added Missing Save Button Div ** --}}
             <div class="form-field">
                 <button type="submit" id="saveEventBtn">Save Event</button>
             </div>
             {{-- ** End Missing Save Button Div ** --}}
        </form>
    </div>
</div>
{{-- @endsection --}}

{{-- @push('scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Elements ---
        const eventModal = document.getElementById('eventModal');
        const eventForm = document.getElementById('eventForm');
        const createEventBtn = document.getElementById('createEventBtn');
        const closeEventModalBtn = document.getElementById('closeEventModalBtn');
        const eventListUl = document.getElementById('event-list');
        const menuBtn = document.getElementById('menuBtn');
        const menuDropdown = document.getElementById('menuDropdown');
        const editEventIdInput = document.getElementById('editEventId');
        const modalMethodField = document.getElementById('modalMethodField');
        const toggleHistoryBtn = document.getElementById('toggleHistoryBtn');
        const eventHistoryDiv = document.getElementById('eventHistory');
        const historyListUl = document.getElementById('history-list');

        // --- CSRF Token ---
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // --- URLs ---
         const urls = {
            store: "{{ route('event-categories.store') }}",
            history: "{{ route('event-categories.history') }}",
            // ** Ensure destroy route is defined in web.php **
            destroyBase: "{{ route('event-categories.destroy', ':id') }}",
            // updateBase: "{{-- route('event-categories.update', ':id') --}}", // Commented
            // getDataBase: "{{-- route('event-categories.data', ':id') --}}", // Commented
        };

        // --- Helper Functions ---
         function escapeHTML(str) {
             const div = document.createElement('div'); div.appendChild(document.createTextNode(str || '')); return div.innerHTML;
         }
         function hideAllDropdowns() {
             eventListUl?.querySelectorAll('.event-dropdown').forEach(d => d.style.display = 'none');
             if(menuDropdown) menuDropdown.style.display = 'none';
         }
         function clearFormErrors() {
             eventForm?.querySelectorAll('.error-message').forEach(span => span.textContent = '');
             eventForm?.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
         }
         function displayFormErrors(errors) {
             clearFormErrors();
             for (const field in errors) {
                 const inputField = eventForm?.querySelector(`[name="${field}"]`);
                 const errorSpan = eventForm?.querySelector(`.error-message[data-field="${field}"]`);
                 if (inputField) inputField.classList.add('is-invalid');
                 if (errorSpan) errorSpan.textContent = errors[field][0];
             }
         }
         function renderEventListItem(event) {
             const title = escapeHTML(event.title || 'Untitled Event'); const id = event.id; if (!id) return '';
             return `<li data-id="${id}" data-title="${title}"><div class="event-title-container"><span class="event-title-text">${title}</span><div class="event-options"><i class="fas fa-ellipsis-v options-icon"></i><div class="event-dropdown" style="display: none;"><button type="button" class="edit-btn" data-id="${id}"><i class="fas fa-edit"></i> Edit</button><button type="button" class="delete-btn" data-id="${id}"><i class="fas fa-trash-alt"></i> Delete</button></div></div></div></li>`;
         }
         function addEventToList(eventData) {
     if (!eventData || !eventData.id) return;
     const newLiHtml = renderEventListItem(eventData); // Creates the HTML for the new item
     const noEventsLi = eventListUl?.querySelector('.no-events');
     if (noEventsLi) noEventsLi.remove(); // Removes the "No active..." message
     eventListUl?.insertAdjacentHTML('afterbegin', newLiHtml); // Adds the new <li> to the top of the <ul>
 }

        // --- Modal Open/Close ---
        function openModalForCreate() {
             eventForm?.reset(); clearFormErrors(); editEventIdInput.value = ''; modalMethodField.value = 'POST'; document.getElementById('modalTitle').textContent = 'Create Event Category'; eventModal.style.display = 'flex'; document.getElementById('event-title')?.focus(); hideAllDropdowns();
        }
        function closeModal() {
             eventModal.style.display = 'none';
        }

        // --- Form Submission Handler (Create) ---
        eventForm?.addEventListener('submit', function(event) {
             event.preventDefault(); const saveButton = eventForm.querySelector('#saveEventBtn'); const formData = new FormData(eventForm); const currentId = editEventIdInput.value; let url = urls.store;
             saveButton.disabled = true; saveButton.textContent = 'Saving...'; clearFormErrors();
             // Add Edit URL logic here later if needed
             if (!url) { alert("Configuration error: Store URL missing."); return; }
             fetch(url, { method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken,'Accept': 'application/json',}, body: formData })
             .then(response => response.json().then(data => ({ status: response.status, ok: response.ok, body: data })))
             .then(result => {
                 if (!result.ok) { if (result.status === 422 && result.body.errors) { displayFormErrors(result.body.errors); } else { alert(result.body.message || `An error occurred (Status: ${result.status}).`); console.error("Error:", result.body); } return; }
                 alert(result.body.message || 'Operation successful!'); closeModal();
                 if (result.body.eventCategory) { addEventToList(result.body.eventCategory); } else { console.warn("Response did not include event data."); window.location.reload(); }
             })
             .catch(error => { console.error('Fetch Error:', error); alert('A network or processing error occurred.'); })
             .finally(() => { saveButton.disabled = false; saveButton.textContent = editEventIdInput.value ? 'Update Event' : 'Save Event'; });
        });

        // --- History Toggle Logic ---
        // --- *** History Toggle Logic (Display adjusted) *** ---
let isHistoryLoaded = false; // Still useful if you fetch only once per page load
toggleHistoryBtn?.addEventListener('click', function(e) {
    e.stopPropagation(); hideAllDropdowns(); const isVisible = eventHistoryDiv.style.display === 'block';
    if (isVisible) {
        eventHistoryDiv.style.display = 'none'; toggleHistoryBtn.innerHTML = '<i class="fas fa-history"></i> View History';
    } else {
        eventHistoryDiv.style.display = 'block'; toggleHistoryBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide History';
        if (!isHistoryLoaded && urls.history) {
            historyListUl.innerHTML = '<li class="no-history">Loading history...</li>';
            fetch(urls.history, { method: 'GET', headers: {'Accept': 'application/json','X-CSRF-TOKEN': csrfToken}})
            .then(response => { if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`); return response.json(); })
            .then(data => {
                historyListUl.innerHTML = '';
                if (data.historyEvents && data.historyEvents.length > 0) {
                    data.historyEvents.forEach(item => {
                        const li = document.createElement('li');
                        // ** Displaying ALL current events in history section **
                        li.textContent = `${escapeHTML(item.title)}`; // Just show title, as it's not deleted
                        historyListUl.appendChild(li);
                    });
                    isHistoryLoaded = true;
                } else {
                    historyListUl.innerHTML = '<li class="no-history">No events found.</li>'; // Adjusted message
                }
            })
            .catch(error => {
                console.error("Error fetching history:", error);
                historyListUl.innerHTML = '<li class="no-history">Could not load event data.</li>'; // Adjusted message
            });
        } else if (!urls.history) { historyListUl.innerHTML = '<li class="no-history">History feature not configured.</li>'; }
    }
});
        // --- *** UPDATED: Delete Event Logic *** ---
function deleteEvent(eventId, listItemElement) {
    const eventTitle = listItemElement?.dataset.title || 'this event';

    if (!urls.destroyBase) { /* ... error check ... */ return; }
    if (!eventId) { /* ... error check ... */ return; }

    // ** Update Confirmation Message **
    if (!confirm(`Are you sure you want to PERMANENTLY delete "${eventTitle}"? This action cannot be undone.`)) {
        return; // Stop if user cancels
    }

    const url = urls.destroyBase.replace(':id', eventId);

    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', }
    })
    .then(response => response.json().then(data => ({ ok: response.ok, status: response.status, body: data })))
    .then(result => {
        if (!result.ok) throw new Error(result.body.message || `Failed to delete (Status: ${result.status})`);

        // Success
        alert(result.body.message || 'Event permanently deleted!');
        listItemElement.remove(); // Remove from UI
        if (eventListUl && eventListUl.children.length === 0) { // Check if list is empty
             eventListUl.innerHTML = '<li class="no-events">No active event categories found.</li>';
         }
        // History doesn't need reload flag now unless its logic changes drastically
    })
    .catch(error => {
        console.error('Error deleting event:', error);
        alert(`Could not delete event: ${error.message}`);
    });
}

        // --- Event Listeners ---
        createEventBtn?.addEventListener('click', openModalForCreate);
        closeEventModalBtn?.addEventListener('click', closeModal);
        eventModal?.addEventListener('click', (e) => { if (e.target === eventModal) closeModal(); });
        menuBtn?.addEventListener('click', (e) => {
             e.stopPropagation(); if (menuDropdown) { menuDropdown.style.display = menuDropdown.style.display === 'flex' ? 'none' : 'flex'; menuDropdown.style.flexDirection = 'column'; }
             if(eventHistoryDiv) eventHistoryDiv.style.display = 'none'; if(toggleHistoryBtn) toggleHistoryBtn.innerHTML = '<i class="fas fa-history"></i> View History';
        });

        // Event delegation for list items
        eventListUl?.addEventListener('click', (e) => {
            const optionsIcon = e.target.closest('.options-icon'); const editBtn = e.target.closest('.edit-btn'); const deleteBtn = e.target.closest('.delete-btn'); const listItem = e.target.closest('li');
            if (optionsIcon) { e.stopPropagation(); const dropdown = optionsIcon.closest('.event-options')?.querySelector('.event-dropdown'); if (dropdown) { const isVisible = dropdown.style.display === 'block'; hideAllDropdowns(); if (!isVisible) dropdown.style.display = 'block'; } }
            else if (editBtn) { e.stopPropagation(); const eventId = editBtn.dataset.id; alert(`Edit Clicked (ID: ${eventId}). Implement edit logic.`); hideAllDropdowns(); }
            else if (deleteBtn && listItem) { e.stopPropagation(); const eventId = deleteBtn.dataset.id; deleteEvent(eventId, listItem); hideAllDropdowns(); }
        });

        // Global click listener
        document.addEventListener('click', (e) => { if (!e.target.closest('.event-options')) { hideAllDropdowns(); } if (!e.target.closest('.dropdown')) { if(menuDropdown) menuDropdown.style.display = 'none'; } });

    }); // End DOMContentLoaded
</script>
{{-- @endpush --}}