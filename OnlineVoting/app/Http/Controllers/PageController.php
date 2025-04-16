<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the contact form page.
     *
     * @return \Illuminate\View\View
     */
    public function showContactForm()
    {
        // Returns resources/views/contact.blade.php
        return view('contact');
    }

    /**
     * Handle the submission of the contact form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContactForm(Request $request)
    {
        // 1. Validate
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // 2. Process (e.g., send email - implement later)
        // For now, just redirecting back is enough to test validation
        // dd($validatedData); // Use this to check data during testing

        // 3. Redirect back with success message
        return back()->with('success', 'Thank you for your message!');
    }

    // You might have other methods here (e.g., home, about)
    // --- Add this new method for Instructions ---
    /**
     * Display the instructions page.
     *
     * @return \Illuminate\View\View
     */
    public function instructions(): View // <<< Add this method
    {
        // Just return the view file named 'instructions.blade.php'
        return view('instructions');
    }

}
