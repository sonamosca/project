@extends('layouts.admin') {{-- Inherit the admin layout --}}

{{-- Define unique sections for the layout --}}
@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Dashboard')
@section('content-id', 'dashboard') {{-- Optional: Sets ID on content-area section --}}

{{-- Define the main content for this page --}}
@section('content')
    <h1>Dashboard</h1>

    <div class="cards-container">
        <!-- Card reflecting Manage Event -->
        <div class="card card-blue">
            <div class="card-body">
                <div class="card-info">
                    {{-- Use the variable passed from the controller --}}
                    <span class="card-number">{{ $eventCount }}</span>
                    <span class="card-title">Total Events</span>
                </div>
                <div class="card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            {{-- Update href later to the actual events route --}}
            <a href="{{-- route('admin.events.index') --}}#events" class="card-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>

        <!-- Card reflecting Add/Update Candidate -->
         <div class="card card-green">
             <div class="card-body">
                 <div class="card-info">
                     <span class="card-number">{{ $candidateCount }}</span>
                     <span class="card-title">No. of Candidates</span>
                 </div>
                 <div class="card-icon">
                     <i class="fas fa-user-tie"></i>
                 </div>
             </div>
            {{-- Update href later --}}
            <a href="{{-- route('admin.candidates.index') --}}#candidates" class="card-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>

        <!-- Card reflecting Manage Voter -->
        <div class="card card-yellow">
            <div class="card-body">
                 <div class="card-info">
                     <span class="card-number">{{ $voterCount }}</span>
                     <span class="card-title">Total Voters</span>
                 </div>
                 <div class="card-icon">
                     <i class="fas fa-users"></i>
                 </div>
             </div>
             {{-- Update href later --}}
            <a href="{{-- route('admin.voters.index') --}}#voters" class="card-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>

         <!-- Card reflecting View Results -->
        <div class="card card-red">
             <div class="card-body">
                 <div class="card-info">
                     <span class="card-number">{{ $resultsAvailableCount }}</span>
                     <span class="card-title">Results Available</span>
                 </div>
                 <div class="card-icon">
                    <i class="fas fa-poll"></i>
                </div>
            </div>
            {{-- Update href later --}}
            <a href="{{-- route('admin.results.index') --}}#results" class="card-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Placeholder for other dashboard content like tables or charts -->
    <div class="content-section">
        <h2>Quick Actions</h2>
         {{-- Update buttons to link to actual creation routes/modals later --}}
         {{-- Use <a> tags for navigation, <button> often implies form submission --}}
        <a href="{{-- route('admin.events.create') --}}#" class="action-button"><i class="fas fa-plus"></i> Add New Event</a>
        <a href="{{-- route('admin.candidates.create') --}}#" class="action-button"><i class="fas fa-user-plus"></i> Add New Candidate</a>
    </div>

    {{-- Comment out or remove the sections below, as they should be separate views/pages --}}
    <!--
    <section class="content-area" id="events" style="display: none;"><h1>Manage Events</h1>...</section>
    <section class="content-area" id="candidates" style="display: none;"><h1>Manage Candidates</h1>...</section>
    <section class="content-area" id="voters" style="display: none;"><h1>Manage Voters</h1>...</section>
    <section class="content-area" id="results" style="display: none;"><h1>View Results</h1>...</section>
    <section class="content-area" id="settings" style="display: none;"><h1>System Settings</h1>...</section>
    -->
@endsection

{{-- Add page-specific scripts or styles if needed --}}
@push('scripts')
    {{-- <script> console.log('Dashboard page specific JS loaded'); </script> --}}
@endpush

@push('styles')
    {{-- <style> .card-number { color: cyan; } </style> --}}
@endpush