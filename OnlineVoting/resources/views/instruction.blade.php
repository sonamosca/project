<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Instructions - JNEC Online Voting System</title>
  {{-- Font Awesome CDN - This is fine as is --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  {{-- Internal CSS - Keeping it inline for this example.
       For larger projects, move this to public/css/app.css or a dedicated file
       and link using <link rel="stylesheet" href="{{ asset('css/yourfile.css') }}">
  --}}
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #dbeafe, #e0f2fe);
      color: #333;
      padding-top: 80px; /* Adjust if nav height changes */
    }

    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background-color: #0f172a;
      color: white;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 1000;
      transition: background 0.3s, box-shadow 0.3s;
    }

     /* ... (keep all your other CSS rules here) ... */
     nav .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
      font-size: 1rem;
      transition: color 0.2s;
    }

    nav .nav-links a:hover {
      color: #93c5fd;
    }

    .hero {
      text-align: center;
      background: linear-gradient(to right, #3b82f6, #60a5fa);
      color: white;
      padding: 100px 20px 60px;
      border-radius: 0 0 30px 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .hero h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .hero h1.show {
      opacity: 1;
      transform: translateY(0);
    }

    .hero p {
      font-size: 1.2rem;
      margin-top: 10px;
    }

    .content {
      max-width: 900px;
      margin: 50px auto;
      padding: 0 20px;
    }

    .message {
      background-color: #dbeafe;
      border-left: 6px solid #3b82f6;
      border-radius: 15px;
      padding: 25px 30px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
      opacity: 0;
      transform: translateY(50px);
      transition: transform 0.6s ease, opacity 0.6s ease;
    }

    .message.show {
      opacity: 1;
      transform: translateY(0);
    }

    .message h2 {
      color: #1e3a8a;
      margin-bottom: 12px;
    }

    .message p {
      font-size: 1.05rem;
      color: #1f2937;
      line-height: 1.6;
    }

    footer {
      background-color: #0f172a;
      color: #e0f2fe;
      text-align: center;
      padding: 20px;
      font-size: 0.95rem;
      margin-top: 50px;
    }

    footer a {
      color: #93c5fd;
      text-decoration: none;
      margin-left: 5px;
    }

    footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      body {
        padding-top: 120px; /* Increased padding top for stacked nav */
      }
      .hero h1 {
        font-size: 2rem;
      }

      .message {
        padding: 20px;
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 20px; /* Adjust padding */
      }

      nav .nav-links {
        margin-top: 10px;
        display: flex; /* Keep links in a row if space allows or wrap */
        flex-wrap: wrap; /* Allow links to wrap on small screens */
        width: 100%; /* Take full width */
        justify-content: flex-start; /* Align links to the start */
      }

      nav .nav-links a {
        margin: 5px 10px 5px 0; /* Adjust margin for better spacing */
      }
    }
  </style>
</head>
<body>

  <nav>
    {{-- Optional Logo - Uncomment and adjust route if needed
    <div class="nav-logo">
      <a href="{{ route('home') }}" style="color: #fff; font-size: 1.5rem; font-weight: bold; text-decoration: none;">JNEC Voting</a>
    </div>
    --}}
    <div class="nav-links">
      {{-- Use the route() helper to generate URLs based on route names --}}
      <a href="{{ url('/') }}">Home</a> {{-- Assuming you have a named route 'home' --}}
      <a href="{{ route('instructions') }}">Instructions</a>
      {{-- Assuming you have named routes 'about' and 'contact' --}}
      {{-- Note: Original HTML had contact.html for both About and Contact, corrected here --}}
      <a href="{{ route('about') }}">About</a>
      <a href="{{ route('contact') }}">Contact</a>
    </div>
  </nav>

  <div class="hero">
    {{-- Added class 'show' directly as window.onload might conflict or be complex with asset bundling --}}
    {{-- Alternatively, keep the JS as is, but ensure it runs after the element exists --}}
    <h1 class="show">Welcome to the JNEC Online Voting System</h1>
    <p>This platform is designed to provide a secure, efficient, and transparent voting experience.</p>
  </div>

  <div class="content">
    <!-- Message 1 -->
    <div class="message">
      <h2>Instructions</h2>
      <p>This platform is designed to provide a secure, efficient, and transparent voting experience. To begin, voters will scan their unique barcode at the polling station to verify their identity. Once verified, they will be granted access to the local network where they can cast their votes.</p>
    </div>
    <!-- Message 2 -->
    <div class="message">
      <p>The system will display various categories such as Girl Councilor, Chief Councilor, and Hostel Councilor. Voters must select one candidate in each category before submitting their vote. If any category is left unselected, the system will prompt them to complete it.</p>
    </div>
    <!-- Message 3 -->
    <div class="message">
      <p>After submission, votes are securely saved in the database, and a confirmation message will appear. The system then automatically counts the votes and sends the final results via email to all registered voters.</p>
    </div>
    <!-- Message 4 -->
    <div class="message">
      <p>For added security and a better user experience, access links will expire after voting, and a thank-you message will be shown. Voters can also view candidate manifestos and event details on the homepage to make informed choices.</p>
    </div>
    <!-- Message 5 -->
    <div class="message">
      <p>This system ensures a smooth and trustworthy electoral process at JNEC.</p>
    </div>
  </div>

  <footer>
    © 2025 JNEC Voting System. All Rights Reserved. |
    {{-- Assuming you have a named route 'privacy' --}}
    <a href="{{ route('privacy') }}">Privacy Policy</a>
  </footer>

  {{-- Inline JavaScript - Keeping it inline for this example.
       For larger projects, move this to public/js/app.js or a dedicated file
       and link using <script src="{{ asset('js/yourfile.js') }}"></script>
  --}}
  <script>
    // Ensure the DOM is fully loaded before running scripts
    document.addEventListener('DOMContentLoaded', function() {

        // Reveal hero title - Simplified from window.onload
        // Since we added 'show' class directly in HTML, this might be redundant,
        // but kept for demonstration if you prefer JS-driven animation start.
        const heroTitle = document.querySelector('.hero h1');
        if (heroTitle) {
             // Optional: remove 'show' class in HTML and uncomment below line
             // heroTitle.classList.add('show');
        }


        // Animate messages on scroll
        const messages = document.querySelectorAll('.message');
        if (messages.length > 0) {
            const observer = new IntersectionObserver(entries => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                // Add a delay based on the index for staggered effect
                setTimeout(() => {
                    entry.target.classList.add('show');
                }, index * 150); // Reduced delay slightly
                // Optionally unobserve after animation to save resources
                // observer.unobserve(entry.target);
                }
            });
            }, { threshold: 0.2 }); // Trigger when 20% of the element is visible

            messages.forEach(msg => observer.observe(msg));
        }
    }); // End DOMContentLoaded
  </script>
</body>
</html>