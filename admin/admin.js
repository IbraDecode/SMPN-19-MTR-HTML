// Admin Panel JavaScript

(function($) {
    "use strict";

    // Toggle the side navigation
    $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        }
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('.sidebar .collapse').collapse('hide');
        }
        
        // Toggle the side navigation when window is resized below 480px
        if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
            $("body").addClass("sidebar-toggled");
            $(".sidebar").addClass("toggled");
            $('.sidebar .collapse').collapse('hide');
        }
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).on('scroll', function() {
        var scrollDistance = $(this).scrollTop();
        if (scrollDistance > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', function(e) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top)
        }, 1000, 'easeInOutExpo');
        e.preventDefault();
    });

})(jQuery);

// Additional admin functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Confirm delete actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
            e.preventDefault();
            var button = e.target.classList.contains('btn-delete') ? e.target : e.target.closest('.btn-delete');
            var itemName = button.getAttribute('data-item') || 'item ini';
            
            if (confirm('Apakah Anda yakin ingin menghapus ' + itemName + '?')) {
                if (button.tagName === 'A') {
                    window.location.href = button.href;
                } else if (button.tagName === 'BUTTON' && button.form) {
                    button.form.submit();
                }
            }
        }
    });

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Character counter for textareas
    var textareas = document.querySelectorAll('textarea[data-max-length]');
    textareas.forEach(function(textarea) {
        var maxLength = parseInt(textarea.getAttribute('data-max-length'));
        var counter = document.createElement('small');
        counter.className = 'text-muted';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            var currentLength = textarea.value.length;
            counter.textContent = currentLength + '/' + maxLength + ' karakter';
            
            if (currentLength > maxLength) {
                counter.className = 'text-danger';
                textarea.value = textarea.value.substring(0, maxLength);
                updateCounter();
            } else if (currentLength > maxLength * 0.9) {
                counter.className = 'text-warning';
            } else {
                counter.className = 'text-muted';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });

    // File upload preview
    var fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var file = e.target.files[0];
            var previewId = input.getAttribute('data-preview');
            var preview = document.getElementById(previewId);
            
            if (file && preview) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Auto-save draft functionality
    var autoSaveForms = document.querySelectorAll('[data-auto-save]');
    autoSaveForms.forEach(function(form) {
        var formId = form.id || 'form_' + Date.now();
        var saveInterval = parseInt(form.getAttribute('data-auto-save')) || 30000; // Default 30 seconds
        
        // Load saved data
        var savedData = localStorage.getItem('draft_' + formId);
        if (savedData) {
            try {
                var data = JSON.parse(savedData);
                Object.keys(data).forEach(function(key) {
                    var field = form.querySelector('[name="' + key + '"]');
                    if (field) {
                        field.value = data[key];
                    }
                });
                showNotification('Draft dimuat dari penyimpanan lokal', 'info');
            } catch (e) {
                console.error('Error loading draft:', e);
            }
        }
        
        // Auto-save
        setInterval(function() {
            var formData = new FormData(form);
            var data = {};
            for (var pair of formData.entries()) {
                data[pair[0]] = pair[1];
            }
            localStorage.setItem('draft_' + formId, JSON.stringify(data));
        }, saveInterval);
        
        // Clear draft on successful submit
        form.addEventListener('submit', function() {
            localStorage.removeItem('draft_' + formId);
        });
    });
});

// Utility functions
function showNotification(message, type = 'success') {
    var notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            var bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }
    }, 5000);
}

function showLoading(button) {
    var originalText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
    button.disabled = true;
    
    return function() {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Export for global use
window.adminUtils = {
    showNotification: showNotification,
    showLoading: showLoading,
    confirmAction: confirmAction
};

