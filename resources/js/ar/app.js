  "use strict";



// ====================== EXTENSIONS ======================= //

// Extension Redirection
if ($('#extensionRedirection').length) {
    $('video').on('ended', () => {
        window.location = $('#extensionRedirection').data('redirection-url');
    });
}

// Modal (Popup) Contact Form
if ($('#contactFormModal').length) {
    $('video').on('ended', () => {
        $('#contactFormModal').modal("show");
    });
}

// Popup Call to Action
if ($('#popupCallToActionModal').length) {
    $('video').on('ended', () => {
        $('#popupCallToActionModal').modal("show");
    });
}