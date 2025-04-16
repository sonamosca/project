@extends('layouts.public') {{-- Use the public layout created above --}}

@section('title', 'Contact Us - JNEC Online Voting System') {{-- Set the title for this page --}}

@section('content') {{-- Define the content for the layout's @yield('content') --}}
    <div class="hero">
        <h1 id="hero-title">Contact Us</h1> {{-- Added ID for easier targeting --}}
        <p>If you have any questions or feedback, please feel free to reach out to us!</p>
    </div>

    <div class="content">
        <!-- Contact Information -->
        <div class="contact-info reveal-item"> {{-- Added class for JS --}}
            <h2>Contact Information</h2>
            <p>If you need assistance, you can contact us through the following channels:</p>
            <p><strong>Phone:</strong> +97517340844</p>
            <p><strong>Email:</strong> csn.jnec@rub.edu.bt</p>
            <p><strong>Address:</strong> JNEC, Samdrupjongkhar, Dewathang, Bhutan</p>
            <p>Our team is available from Monday to Friday, 9 AM to 6 PM.</p>
        </div>

        <!-- Contact Form -->
        <div class="contact-form reveal-item"> {{-- Added class for JS --}}
            <h2>Send Us a Message</h2>
            {{-- Use Laravel route for form submission and add CSRF protection --}}
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf {{-- Add CSRF token --}}

                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" value="{{ old('name') }}" required>
                {{-- Add error handling if needed later: @error('name') ... @enderror --}}

                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                {{-- Add error handling if needed later: @error('email') ... @enderror --}}

                <label for="message">Your Message</label>
                <textarea id="message" name="message" rows="4" placeholder="Enter your message" required>{{ old('message') }}</textarea>
                {{-- Add error handling if needed later: @error('message') ... @enderror --}}

                <button type="submit">Submit</button>
            </form>
             {{-- Add success message display if needed later: @if(session('success')) ... @endif --}}
        </div>
    </div>
@endsection

@push('scripts') {{-- Add the page-specific JS to the 'scripts' stack in the layout --}}
    <script src="{{ asset('js/contact-animations.js') }}"></script> {{-- We'll create this JS file --}}
@endpush

{{-- You could potentially push page-specific styles too if needed --}}
{{--
@push('styles')
    <style>
        .hero { background: #some-other-color; }
    </style>
@endpush
--}}