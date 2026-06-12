jQuery(document).ready(function($) {
    
    // 1. ADD NEW FORM SCRIPT (Drag-and-Drop Sortable Rows)
    if ($('#stlcf-fields-container').length > 0) {
        $('#stlcf-fields-container').sortable({ 
            handle: '.dashicons-menu', 
            placeholder: 'ui-state-highlight' 
        });
        
        var rowIndex = $('#stlcf-fields-container .stlcf-field-row').length;
        
        $('#stlcf-add-field-btn').on('click', function() {
            var newRow = '<div class="stlcf-field-row">' +
                '<span class="dashicons dashicons-menu"></span>' +
                '<select name="stlcf_fields[' + rowIndex + '][type]">' +
                    '<option value="text">Text Field</option>' +
                    '<option value="email">Email Field</option>' +
                    '<option value="textarea">Textarea</option>' +
                    '<option value="number">Number</option>' +
                '</select>' +
                '<input type="text" name="stlcf_fields[' + rowIndex + '][label]" placeholder="Custom Field Label" required>' +
                '<label><input type="checkbox" name="stlcf_fields[' + rowIndex + '][required]" value="1"> Required</label>' +
                '<button type="button" class="button remove-field-row">Delete</button>' +
            '</div>';
            
            $('#stlcf-fields-container').append(newRow);
            rowIndex++;
        });
        
        $(document).on('click', '.remove-field-row', function() {
            if ($('#stlcf-fields-container .stlcf-field-row').length > 1) { 
                $(this).closest('.stlcf-field-row').remove(); 
            } else { 
                alert('Your form must contain at least one input field row.'); 
            }
        });
    }

    // 2. SETTINGS PAGE SCRIPT (Tabs Navigation Engine)
    $('.stlcf-nav-tab').on('click', function(e) {
        e.preventDefault();
        var targetSectionId = $(this).attr('data-tab');
        
        // Bulletproof check: Only run if data-tab actually exists
        if (targetSectionId) {
            $('.stlcf-nav-tab').removeClass('nav-tab-active');
            $('.stlcf-tab-section').addClass('stlcf-hide-tab');
            
            $(this).addClass('nav-tab-active');
            $('#' + targetSectionId).removeClass('stlcf-hide-tab');
            
            window.location.hash = $(this).attr('href');
        }
    });

    // Support deep-linking from hash anchor parameters
    var activeHash = window.location.hash;
    if (activeHash) {
        var correspondingTab = $('.stlcf-nav-tab[href="' + activeHash + '"]');
        if (correspondingTab.length) {
            correspondingTab.trigger('click');
        }
    }

    // Captcha selection fields evaluation
    var captchaSelector = $('#stlcf_captcha_type');
    function computeCaptchaVisibility() {
        var selectedValue = captchaSelector.val();
        $('.stlcf-captcha-row').hide();
        
        if (selectedValue === 'turnstile') {
            $('.stlcf-row-turnstile').show();
        } else if (selectedValue === 'recaptcha') {
            $('.stlcf-row-recaptcha').show();
        } else if (selectedValue === 'recaptcha_v3') {
            $('.stlcf-row-recaptcha-v3').show();
        }
    }

    if (captchaSelector.length > 0) {
        captchaSelector.on('change', computeCaptchaVisibility);
        computeCaptchaVisibility();
    }

    // 3. FORMS LISTING SCRIPT (Backward Compatible Shortcode Copy)
    if ($('.stlcf-copy-btn').length > 0) {
        $('.stlcf-copy-btn').on('click', function() {
            var shortcode = $(this).attr('data-clipboard');
            var $btn = $(this);
            var $icon = $btn.find('.dashicons');
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shortcode).then(function() {
                    $icon.removeClass('dashicons-admin-page').addClass('dashicons-saved');
                    setTimeout(function() {
                        $icon.removeClass('dashicons-saved').addClass('dashicons-admin-page');
                    }, 1500);
                });
            } else {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(shortcode).select();
                document.execCommand('copy');
                $temp.remove();
                
                $icon.removeClass('dashicons-admin-page').addClass('dashicons-saved');
                setTimeout(function() {
                    $icon.removeClass('dashicons-saved').addClass('dashicons-admin-page');
                }, 1500);
            }
        });
    }
});