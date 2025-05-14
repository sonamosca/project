@extends('layouts.polling')

@section('title', 'Sub Administrator Dashboard')

{{-- NO @push('styles') with layout or card styles here anymore --}}
{{-- Any STYLES VERY SPECIFIC to ONLY this dashboard page could go in a @push, but card styles are in polling.css now --}}

@section('content')
    {{-- The <h1> for the title is now in layouts.polling.blade.php --}}
    {{-- So we can remove the <h2> from here if the layout handles it. --}}
    {{-- Or keep it if you want a sub-heading style --}}
    {{-- <h2>Sub Administrator Dashboard</h2> --}}
    <div class="dashboard-cards">
        <div class="dash-card card-blue">
            <i class="fas fa-calendar-alt card-icon"></i>
            <div> {{-- Wrapper for text content if needed for flex styling in card --}}
                <h3>Event Monitoring</h3>
                <span class="value">Election 2024</span>
                <span class="subtext">Status: Ongoing</span>
            </div>
            <a href="{{ route('po.selectEvent') }}" class="card-link">View Event Details <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dash-card card-green">
             <i class="fas fa-users card-icon"></i>
             <div>
                <h3>Candidate Overview</h3>
                <span class="value">15</span>
                <span class="subtext">Candidates for active event.</span>
            </div>
            <a href="{{ route('po.candidates.view.index') }}" class="card-link">Access Candidate Lists <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        <div class="dash-card card-orange">
             <i class="fas fa-qrcode card-icon"></i>
             <div>
                <h3>Voter Scan Operations</h3>
                <span class="value">Ready</span>
                <span class="subtext">Scans Today: 87</span>
            </div>
            <a href="{{ route('po.selectEvent') }}" class="card-link">Go to Scanning Interface <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
@endsection