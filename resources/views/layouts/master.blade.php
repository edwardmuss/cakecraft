<!doctype html >
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title') | {{ \App\Support\Brand::name() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ \App\Support\Brand::name() }} - invoicing, orders and catalogue management." name="description" />
    <meta content="{{ \App\Support\Brand::name() }}" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:image" itemprop="image"
      content="{{ \App\Support\Brand::logoUrl() }}">
   <meta property="og:url" content="{{ url()->current() }}" />
   <meta property="og:type" content="website" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ \App\Support\Brand::logoUrl() }}">
    @include('layouts.head-css')
</head>

@section('body')
    @include('layouts.body')
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    @include('layouts.customizer')

    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')

    <script>
        function bootGlobalWhatsappEcho() {
            if (window.__waEchoBooted) return;
            window.__waEchoBooted = true;

            const accountId = Number(@json(Auth::user()->branch_id));

            window.Echo.private(`whatsapp.account.${accountId}`)
                .listen('.whatsapp.account.message.created', (payload) => {
                window.dispatchEvent(new CustomEvent('wa:account_message', { detail: payload }));
                })
                .listen('.whatsapp.account.message.updated', (payload) => {
                window.dispatchEvent(new CustomEvent('wa:account_message_updated', { detail: payload }));
                });
        };

        // Get the session lifetime from Laravel (in minutes)
        var sessionLifetimeInMinutes = {{ config('session.lifetime') }};
        var warningTimeInMinutes = 1; // Time before expiration to show the warning (e.g., 1 minute)
        
        var sessionWarningTimer;
    
        // Function to check session status without refreshing it
        function checkSession() {
            fetch('/session/check')  // This route checks if the session is active, without refreshing it
                .then(response => response.json())
                .then(data => {
                    if (!data.authenticated) {
                        // If session has expired, show the warning and log out
                        showSessionExpiryWarning();
                    }
                });
        }
    
        // Log the user out by posting to the logout route
        function logout() {
            // Save the current page's URL in sessionStorage
            sessionStorage.setItem('previousPage', window.location.href);
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                window.location.href = '/login';
            });
        }

        // Check if there's a previous page stored in sessionStorage
        function checkPreviousPage(){
            const previousPage = sessionStorage.getItem('previousPage');
            if (previousPage) {
                // Redirect to the previous page
                window.location.href = previousPage;
                // Remove it from sessionStorage to prevent future redirects
                sessionStorage.removeItem('previousPage');
            }
        }
    
        // Refresh the session if the user wants to stay logged in
        function refreshSession() {
            fetch('/session/refresh', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      console.log("Session refreshed");
                      // Reset session expiration warning timer after refreshing the session
                      resetSessionWarningTimer();
                  }
              });
        }
    
        // Show session expiration warning to the user
        function showSessionExpiryWarning() {
            var countdown = 60; // Countdown time in seconds

            const timerInterval = setInterval(() => {
                countdown--;
                Swal.getHtmlContainer().querySelector('b').textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timerInterval);
                }
            }, 1000);

            Swal.fire({
                title: 'Session Expiring Soon',
                html: `Your session will expire in <b>${countdown}</b> seconds. Do you want to stay logged in?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Stay Logged In',
                cancelButtonText: 'Log Out',
                allowOutsideClick: false,
                timer: countdown * 1000,  // Display warning for 60 seconds
                willClose: () => {
                    clearInterval(timerInterval); // Clear the interval when the Swal is closed
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh session if user chooses to stay logged in
                    refreshSession();
                } else if (result.dismiss === Swal.DismissReason.timer || result.isDismissed) {
                    // Log out if the timer ran out or the user dismissed the alert
                    logout();
                }
            });
        }
        // Reset the session warning timer dynamically
        function resetSessionWarningTimer() {
            clearTimeout(sessionWarningTimer);
            sessionWarningTimer = setTimeout(showSessionExpiryWarning, (sessionLifetimeInMinutes - warningTimeInMinutes) * 60000);
        }

        // Start the warning timer X minutes before the session expires
        sessionWarningTimer = setTimeout(showSessionExpiryWarning, (sessionLifetimeInMinutes - warningTimeInMinutes) * 60000);
    
        // Periodically check session every 60 seconds (adjust as needed)
        setInterval(checkSession, 60000);

        checkPreviousPage()
    </script>
    
    
</body>

</html>
