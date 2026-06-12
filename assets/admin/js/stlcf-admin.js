jQuery(document).ready(function($) {
    
    // 1. ADD NEW FORM SCRIPT
    if ($('#stlcf-fields-container').length > 0) {
        $('#stlcf-fields-container').sortable({ handle: '.dashicons-menu', placeholder: 'ui-state-highlight' });
        var rowIndex = $('#stlcf-fields-container .stlcf-field-row').length;
        
        $('#stlcf-add-field-btn').on('click', function() {
            var newRow = '<div class="stlcf-field-row" style="background: #f8fafc; padding: 15px; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 10px; cursor: move; display: flex; align-items: center; gap: 15px;"><span class="dashicons dashicons-menu" style="color: #94a3b8;"></span><select name="stlcf_fields[' + rowIndex + '][type]" style="height: 35px;"><option value="text">Text Field</option><option value="email">Email Field</option><option value="textarea">Textarea</option><option value="number">Number</option></select><input type="text" name="stlcf_fields[' + rowIndex + '][label]" placeholder="Custom Field Label" style="flex: 1; height: 35px;" required><label><input type="checkbox" name="stlcf_fields[' + rowIndex + '][required]" value="1"> Required</label><button type="button" class="button remove-field-row" style="color:red; border-color:red;">Delete</button></div>';
            $('#stlcf-fields-container').append(newRow);
            rowIndex++;
        });
        
        $(document).on('click', '.remove-field-row', function() {
            if($('#stlcf-fields-container .stlcf-field-row').length > 1) { 
                $(this).closest('.stlcf-field-row').remove(); 
            } else { 
                alert('Your form must contain at least one input field row.'); 
            }
        });
    }

    // 2. SETTINGS PAGE SCRIPT (Dynamic Captcha)
    if ($('#stlcf_captcha_type').length > 0) {
        function handleDynamicCaptchaFieldsToggle() {
            var activeSelection = $('#stlcf_captcha_type').val();
            $('.stlcf-captcha-row').hide();
            if (activeSelection === 'turnstile') { $('.stlcf-row-turnstile').show(); } 
            else if (activeSelection === 'recaptcha') { $('.stlcf-row-recaptcha').show(); } 
            else if (activeSelection === 'recaptcha_v3') { $('.stlcf-row-recaptcha-v3').show(); }
        }
        $('#stlcf_captcha_type').on('change', handleDynamicCaptchaFieldsToggle);
        handleDynamicCaptchaFieldsToggle();
    }

    // 3. FORMS LISTING SCRIPT (Copy Shortcode)
    if ($('.stlcf-copy-btn').length > 0) {
        $('.stlcf-copy-btn').on('click', function() {
            var shortcode = $(this).attr('data-clipboard');
            var $btn = $(this);
            var $icon = $btn.find('.dashicons');
            
            navigator.clipboard.writeText(shortcode).then(function() {
                $icon.removeClass('dashicons-admin-page').addClass('dashicons-saved');
                $icon.css('color', '#16a34a');
                setTimeout(function() {
                    $icon.removeClass('dashicons-saved').addClass('dashicons-admin-page');
                    $icon.css('color', '#475569');
                }, 1500);
            });
        });
    }
});