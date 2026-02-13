// Import SweetAlert2
import Swal from "sweetalert2";

// Make Swal available globally
window.Swal = Swal;

// jQuery and Select2 are loaded via CDN in the layout
// This ensures they're available globally before any code runs
// We just need to wait for them to be available

// Wait for jQuery and Select2 to be available (from CDN)
function waitForSelect2(callback, maxAttempts = 50) {
    if (
        typeof window.jQuery !== "undefined" &&
        typeof window.jQuery.fn.select2 !== "undefined"
    ) {
        callback();
    } else if (maxAttempts > 0) {
        setTimeout(() => waitForSelect2(callback, maxAttempts - 1), 100);
    } else {
        console.error(
            "Select2 failed to load. Please check your internet connection or CDN availability."
        );
    }
}

// Wait for DOM and ensure jQuery and Select2 are ready
document.addEventListener("DOMContentLoaded", function () {
    waitForSelect2(() => {
        initializeSelect2();
    });
});

// SweetAlert helper function for flexible alerts
// Usage examples:
// showSweetAlert('Pesan sederhana')
// showSweetAlert({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan' })
// showSweetAlert({ icon: 'warning', title: 'Peringatan', text: 'Perhatian!', showCancelButton: true })
window.showSweetAlert = function (options) {
    const defaultOptions = {
        icon: "success",
        title: "Berhasil!",
        text: "",
        confirmButtonText: "OK",
        confirmButtonColor: "#0891b2",
        showCancelButton: false,
        cancelButtonText: "Batal",
        cancelButtonColor: "#6c757d",
        timer: null,
        timerProgressBar: false,
        allowOutsideClick: true,
        allowEscapeKey: true,
        showConfirmButton: true,
        showCloseButton: false,
        width: null,
        padding: null,
        backdrop: true,
        customClass: {},
        buttonsStyling: true,
    };

    // Handle different parameter formats
    let mergedOptions;

    // If options is a string, treat it as text
    if (typeof options === "string") {
        console.log(options);
        mergedOptions = { ...defaultOptions, text: options };
    }
    // If options is an object with just message property, use it as text
    else if (
        options &&
        typeof options === "object" &&
        options.message &&
        Object.keys(options).length === 1
    ) {
        mergedOptions = { ...defaultOptions, text: options.message };
    }
    // Otherwise, merge with defaults
    else {
        let data = options[0];
        mergedOptions = { ...defaultOptions, ...data };
    }

    return Swal.fire(mergedOptions);
};

// Reusable function for delete confirmation with SweetAlert
// Usage:
// 1. Automatic: Add class 'btn-delete' and data-delete-url attribute to any element
//    <a href="#" class="btn-delete" data-delete-url="/delete/1">Delete</a>
// 2. Manual: Call confirmDelete(url, options) directly
//    confirmDelete('/delete/1', { title: 'Custom Title', text: 'Custom message' })
window.confirmDelete = function (url, options = {}) {
    const defaultOptions = {
        icon: "warning",
        title: "Apakah Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6c757d",
        showCancelButton: true,
        reverseButtons: true,
    };

    const mergedOptions = { ...defaultOptions, ...options };

    return Swal.fire(mergedOptions).then((result) => {
        if (result.isConfirmed && url) {
            // Create a form to submit DELETE request
            const form = document.createElement("form");
            form.method = "POST";
            form.action = url;

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement("input");
                csrfInput.type = "hidden";
                csrfInput.name = "_token";
                csrfInput.value = csrfToken.getAttribute("content");
                form.appendChild(csrfInput);
            } else {
                // Fallback: try to get from Laravel's global variable or use jQuery
                const $ = window.jQuery;
                if ($ && $.ajaxSetup) {
                    const token = $('meta[name="csrf-token"]').attr("content");
                    if (token) {
                        const csrfInput = document.createElement("input");
                        csrfInput.type = "hidden";
                        csrfInput.name = "_token";
                        csrfInput.value = token;
                        form.appendChild(csrfInput);
                    }
                }
            }

            // Add method spoofing for DELETE
            const methodInput = document.createElement("input");
            methodInput.type = "hidden";
            methodInput.name = "_method";
            methodInput.value = "DELETE";
            form.appendChild(methodInput);

            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        }
    });
};

// Initialize delete button handlers using event delegation
// This automatically handles all elements with class 'btn-delete' and data-delete-url attribute
function initializeDeleteButtons() {
    // Use event delegation to handle dynamically loaded content (like DataTables)
    document.addEventListener("click", function (e) {
        const deleteButton = e.target.closest(".btn-delete");
        if (deleteButton) {
            e.preventDefault();
            const deleteUrl = deleteButton.getAttribute("data-delete-url");
            if (deleteUrl) {
                // Get custom options from data attributes if present
                const customTitle = deleteButton.getAttribute("data-title");
                const customText = deleteButton.getAttribute("data-text");
                const customConfirmText = deleteButton.getAttribute(
                    "data-confirm-text"
                );

                const options = {};
                if (customTitle) {
                    options.title = customTitle;
                }
                if (customText) {
                    options.text = customText;
                }
                if (customConfirmText) {
                    options.confirmButtonText = customConfirmText;
                }

                confirmDelete(deleteUrl, options);
            } else {
                console.warn(
                    "btn-delete element is missing data-delete-url attribute"
                );
            }
        }
    });
}

// Handle Laravel sweet-alert package alerts (from session)
// This function reads alert data from the page and displays it using SweetAlert2
function handleLaravelAlerts() {
    // Check if there's alert data in the page (from sweetalert::alert view)
    // Package realrashid/sweet-alert usually injects script tags with alert data
    const alertScripts = document.querySelectorAll('script[data-swal]');
    
    if (alertScripts.length > 0) {
        alertScripts.forEach((script) => {
            try {
                const alertData = JSON.parse(script.textContent || script.innerHTML);
                if (alertData && alertData.type) {
                    // Map Laravel sweet-alert types to SweetAlert2 icons
                    const iconMap = {
                        'success': 'success',
                        'error': 'error',
                        'warning': 'warning',
                        'info': 'info',
                        'question': 'question'
                    };

                    const alertOptions = {
                        icon: iconMap[alertData.type] || 'info',
                        title: alertData.title || (alertData.type === 'success' ? 'Berhasil!' : 'Perhatian!'),
                        text: alertData.text || alertData.message || '',
                        confirmButtonText: alertData.confirmButtonText || 'OK',
                        confirmButtonColor: alertData.type === 'success' ? '#0891b2' : '#ef4444',
                        timer: alertData.timer || null,
                        timerProgressBar: alertData.timer ? true : false,
                    };

                    Swal.fire(alertOptions);
                }
            } catch (e) {
                console.warn('Failed to parse alert data:', e);
            }
        });
    }

    // Also check for window.swalData (if package sets it)
    if (window.swalData) {
        const alertData = window.swalData;
        const iconMap = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info',
            'question': 'question'
        };

        Swal.fire({
            icon: iconMap[alertData.type] || 'info',
            title: alertData.title || 'Alert',
            text: alertData.text || alertData.message || '',
            confirmButtonText: 'OK',
        });

        // Clear the data
        window.swalData = null;
    }
}

// Initialize delete buttons on DOM ready
document.addEventListener("DOMContentLoaded", function () {
    initializeDeleteButtons();
    // Handle Laravel sweet-alert package alerts (from session)
    handleLaravelAlerts();
});

// Handler for menu toggle clicks (defined once, reused)
function handleMenuToggleClick(e) {
    const menuToggleLink = e.target.closest(".menu-link.menu-toggle");
    if (!menuToggleLink) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();

    const menuItem = menuToggleLink.closest(".menu-item");
    if (menuItem) {
        // Toggle the open class
        menuItem.classList.toggle("open");
    }
}

// Initialize menu toggles using event delegation
let menuToggleInitialized = false;
function initializeMenuToggles() {
    // Use event delegation on the menu container to handle all menu toggles
    const menuContainer = document.querySelector("#layout-menu");
    if (!menuContainer) {
        return;
    }

    // Only add the event listener once
    if (!menuToggleInitialized) {
        menuContainer.addEventListener("click", handleMenuToggleClick);
        menuToggleInitialized = true;
    }
}

// Re-initialize Select2 after Livewire updates and listen for events
document.addEventListener("livewire:init", () => {
    // Listen for form submission success (backward compatibility)
    Livewire.on("form-submitted-success", () => {
        showSweetAlert({
            icon: "success",
            title: "Berhasil!",
            text: "Formulir pasang baru berhasil dikirim. Tim kami akan segera menghubungi Anda.",
        });
    });

    // Listen for generic sweetalert event with parameters
    Livewire.on("sweetalert", (data) => {
        console.log(data);
        showSweetAlert(data);
    });

    // Reinitialize after DOM morphing (including validation errors)
    Livewire.hook("morph.updated", ({ el, component }) => {
        waitForSelect2(() => {
            const $ = window.jQuery;
            // Find all select2-select elements that need reinitialization
            $(el)
                .find(".select2-select")
                .each(function () {
                    const $select = $(this);
                    // If Select2 was destroyed (no longer has select2 classes), reinitialize
                    if (!$select.hasClass("select2-hidden-accessible")) {
                        // Destroy any existing instance first
                        if ($select.data("select2")) {
                            $select.select2("destroy");
                        }
                        initSelect2Element($select);
                    }
                });
        });

        // Reinitialize menu toggles after Livewire updates (if container still exists)
        setTimeout(() => {
            if (document.querySelector("#layout-menu")) {
                initializeMenuToggles();
            }
        }, 100);
    });

    // Also reinitialize after message processing (validation errors)
    Livewire.hook("message.processed", (message, component) => {
        waitForSelect2(() => {
            const $ = window.jQuery;
            // Reinitialize all Select2 elements that were destroyed
            $(".select2-select").each(function () {
                const $select = $(this);
                if (!$select.hasClass("select2-hidden-accessible")) {
                    if ($select.data("select2")) {
                        $select.select2("destroy");
                    }
                    initSelect2Element($select);
                }
            });
        });

        // Reinitialize menu toggles (if container still exists)
        setTimeout(() => {
            if (document.querySelector("#layout-menu")) {
                initializeMenuToggles();
            }
        }, 100);
    });
});

// Initialize menu toggles on DOM ready
document.addEventListener("DOMContentLoaded", function () {
    // Wait for menu.js and main.js to initialize first
    // Then initialize our menu toggle handler
    function waitForMenuInit() {
        if (document.querySelector("#layout-menu")) {
            initializeMenuToggles();
        } else {
            setTimeout(waitForMenuInit, 100);
        }
    }

    // Start checking after a short delay to let other scripts load
    setTimeout(waitForMenuInit, 300);
});

function initSelect2Element($element) {
    if (typeof $element.select2 === "function") {
        // Store current value before destroying
        const currentValue = $element.val();

        // Destroy existing instance if it exists
        if ($element.data("select2")) {
            $element.select2("destroy");
        }

        // Initialize Select2
        $element.select2({
            theme: "default",
            width: "100%",
            language: {
                noResults: function () {
                    return "Tidak ada hasil";
                },
                searching: function () {
                    return "Mencari...";
                },
            },
        });

        // Restore value if it exists
        if (currentValue) {
            $element.val(currentValue).trigger("change");
        }
    } else {
        console.warn(
            "Select2 is not available on this element. Make sure jQuery and Select2 are loaded correctly."
        );
    }
}

function initializeSelect2() {
    if (
        typeof window.jQuery === "undefined" ||
        typeof window.jQuery.fn.select2 === "undefined"
    ) {
        console.warn(
            "Select2 is not available. Make sure jQuery and Select2 are loaded correctly."
        );
        return;
    }

    const $ = window.jQuery;
    $(".select2-select").each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initSelect2Element($(this));
        }
    });
}
