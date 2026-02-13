@php
    $alert = null;
    $alertData = null;
    
    // Try different session keys that the package might use
    if (Session::has('alert.config')) {
        $alert = Session::get('alert.config');
        // Package stores data as JSON string, decode it
        if (is_string($alert)) {
            $alertData = json_decode($alert, true);
        } else {
            $alertData = $alert;
        }
    } elseif (Session::has('sweet_alert')) {
        $alert = Session::get('sweet_alert');
        if (is_string($alert)) {
            $alertData = json_decode($alert, true);
        } else {
            $alertData = $alert;
        }
    } elseif (Session::has('alert')) {
        $alert = Session::get('alert');
        if (is_string($alert)) {
            $alertData = json_decode($alert, true);
        } else {
            $alertData = $alert;
        }
    }
@endphp

@if ($alertData)
    <script>
        (function() {
            const alertData = @json($alertData);
            
            function showAlert() {
                if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                    // Map alert types to SweetAlert2 icons
                    const iconMap = {
                        'success': 'success',
                        'error': 'error',
                        'warning': 'warning',
                        'info': 'info',
                        'question': 'question'
                    };

                    // Package realrashid/sweet-alert stores: title, text, icon, timer, etc.
                    const alertType = alertData.icon || alertData.type || 'info';
                    const alertTitle = alertData.title || '';
                    const alertText = alertData.text || alertData.message || '';

                    const options = {
                        icon: iconMap[alertType] || 'info',
                        title: alertTitle,
                        text: alertText,
                        confirmButtonText: alertData.confirmButtonText || 'OK',
                        confirmButtonColor: alertType === 'success' ? '#0891b2' : (alertType === 'error' ? '#ef4444' : '#0891b2'),
                        showConfirmButton: alertData.showConfirmButton !== false,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                    };

                    // Add timer if specified
                    if (alertData.timer) {
                        options.timer = alertData.timer;
                        options.timerProgressBar = alertData.timerProgressBar || false;
                    }

                    // Add cancel button if needed
                    if (alertData.showCancelButton) {
                        options.showCancelButton = true;
                        options.cancelButtonText = alertData.cancelButtonText || 'Batal';
                        options.cancelButtonColor = '#6c757d';
                    }

                    Swal.fire(options);
                    return true;
                }
                return false;
            }

            // Try to show alert immediately if Swal is already loaded
            if (!showAlert()) {
                // Wait for DOM and Swal to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        // Wait a bit more for app.js to load Swal
                        setTimeout(function() {
                            if (!showAlert()) {
                                console.warn('SweetAlert2 is not loaded. Please ensure app.js is loaded correctly.');
                            }
                        }, 100);
                    });
                } else {
                    // DOM is already ready, wait for Swal
                    setTimeout(function() {
                        if (!showAlert()) {
                            console.warn('SweetAlert2 is not loaded. Please ensure app.js is loaded correctly.');
                        }
                    }, 100);
                }
            }
        })();
    </script>
@endif
