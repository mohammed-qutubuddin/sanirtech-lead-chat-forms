/**
 * SANIRTECH FORMS - MASTER CONTROL UTILITY ENGINE
 * Core administrative JavaScript file managing Drag-and-Drop Form repeaters,
 * smooth dynamic tab routing, and responsive visibility options toggles.
 */
jQuery(document).ready(function($) {

    // ==========================================================================
    // 1. ADD NEW FORM ENGINE (Drag-and-Drop Repeater Fields Structure)
    // ==========================================================================
    if ($('#stlcf-fields-container').length > 0) {
        
        $('#stlcf-fields-container').sortable({ 
            handle: '.dashicons-menu', 
            placeholder: 'ui-state-highlight' 
        });
        
        var rowIndex = $('#stlcf-fields-container .stlcf-field-row').length;
        
        $('#stlcf-add-field-btn').on('click', function() {
            var isAgentEnabled = $('#stlcf-fields-container').attr('data-agent-routing');
            var agentOptionMarkup = '';
            
            if (isAgentEnabled === '1') {
                agentOptionMarkup = '<option value="agent_select">Agent Dropdown Routing</option>';
            }

            var newRow = '<div class="stlcf-field-row">' +
                '<span class="dashicons dashicons-menu"></span>' +
                '<select name="stlcf_fields[' + rowIndex + '][type]">' +
                    '<option value="text">Text Field</option>' +
                    '<option value="email">Email Field</option>' +
                    '<option value="textarea">Textarea</option>' +
                    '<option value="number">Number</option>' +
                    agentOptionMarkup +
                '</select>' +
                '<input type="text" name="stlcf_fields[' + rowIndex + '][label]" placeholder="Custom Field Label" required>' +
                '<label><input type="checkbox" name="stlcf_fields[' + rowIndex + '][required]" value="1"> Required</label>' +
                '<button type="button" class="button remove-field-row">Delete</button>' +
                '<div class="stlcf-routing-config stlcf-hide-field">' +
                    '<p class="description">Define Agents Distribution configuration map (One entry per line):</p>' +
                    '<textarea name="stlcf_fields[' + rowIndex + '][routing]" placeholder="Format: Name|Phone (With country code, no spaces)\nExample:\nSales Team|919999999999\nSupport Desk|918888888888"></textarea>' +
                '</div>' +
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

        $(document).on('change', '#stlcf-fields-container select', function() {
            var selectedType = $(this).val();
            var $routingBlock = $(this).closest('.stlcf-field-row').find('.stlcf-routing-config');
            if (selectedType === 'agent_select') {
                $routingBlock.removeClass('stlcf-hide-field');
            } else {
                $routingBlock.addClass('stlcf-hide-field');
            }
        });
    }

    // ==========================================================================
    // 2. DASHBOARD SETTINGS ENGINE (Tabs Navigation System)
    // ==========================================================================
    $('.stlcf-nav-tab').on('click', function(e) {
        e.preventDefault();
        var targetSectionId = $(this).attr('data-tab');
        if (targetSectionId) {
            $('.stlcf-nav-tab').removeClass('nav-tab-active');
            $('.stlcf-tab-section').addClass('stlcf-hide-tab');
            
            $(this).addClass('nav-tab-active');
            $('#' + targetSectionId).removeClass('stlcf-hide-tab');
            window.location.hash = $(this).attr('href');
        }
    });

    var activeHash = window.location.hash;
    if (activeHash) {
        var correspondingTab = $('.stlcf-nav-tab[href="' + activeHash + '"]');
        if (correspondingTab.length) { correspondingTab.trigger('click'); }
    }

    // ==========================================================================
    // 3. SECURE CONDITIONAL SETTINGS FIELDS TOGGLES (UI Visibility Handlers)
    // ==========================================================================
    var captchaSelector = $('#stlcf_captcha_type');
    function computeCaptchaVisibility() {
        var selectedValue = captchaSelector.val();
        $('.stlcf-captcha-row').hide();
        if (selectedValue === 'turnstile') { $('.stlcf-row-turnstile').show(); } 
        else if (selectedValue === 'recaptcha') { $('.stlcf-row-recaptcha').show(); } 
        else if (selectedValue === 'recaptcha_v3') { $('.stlcf-row-recaptcha-v3').show(); }
    }
    if (captchaSelector.length > 0) {
        captchaSelector.on('change', computeCaptchaVisibility);
        computeCaptchaVisibility();
    }

    var hoursMasterToggle = $('#stlcf_enable_business_hours');
    var offlineActionSelector = $('#stlcf_offline_action');
    function computeHoursFieldsVisibility() {
        if (hoursMasterToggle.is(':checked')) {
            $('.stlcf-hours-conditional-row').show();
            if (offlineActionSelector.val() === 'show_notice') {
                $('.id-stlcf-offline-msg-row').show();
            } else {
                $('.id-stlcf-offline-msg-row').hide();
            }
        } else {
            $('.stlcf-hours-conditional-row').hide();
        }
    }
    if (hoursMasterToggle.length > 0) {
        hoursMasterToggle.on('change', computeHoursFieldsVisibility);
        offlineActionSelector.on('change', computeHoursFieldsVisibility);
        computeHoursFieldsVisibility();
    }

    var analyticsToggle = $('#stlcf_enable_pixels_tracking');
    function computeAnalyticsVisibility() {
        if (analyticsToggle.is(':checked')) {
            $('.stlcf-analytics-conditional-row').show();
        } else {
            $('.stlcf-analytics-conditional-row').hide();
        }
    }
    if (analyticsToggle.length > 0) {
        analyticsToggle.on('change', computeAnalyticsVisibility);
        computeAnalyticsVisibility();
    }

    var gdprMasterToggle = $('#stlcf_enable_gdpr');
    function computeGdprFieldsVisibility() {
        if (gdprMasterToggle.is(':checked')) {
            $('.stlcf-gdpr-conditional-row').show();
        } else {
            $('.stlcf-gdpr-conditional-row').hide();
        }
    }
    if (gdprMasterToggle.length > 0) {
        gdprMasterToggle.on('change', computeGdprFieldsVisibility);
        computeGdprFieldsVisibility();
    }

    var widgetMasterToggle = $('#stlcf_floating_btn');
    function computeWidgetFieldsVisibility() {
        if (widgetMasterToggle.is(':checked')) {
            $('.stlcf-widget-conditional-row').show();
        } else {
            $('.stlcf-widget-conditional-row').hide();
        }
    }
    if (widgetMasterToggle.length > 0) {
        widgetMasterToggle.on('change', computeWidgetFieldsVisibility);
        computeWidgetFieldsVisibility();
    }

    var responderToggle = $('#stlcf_enable_auto_responder');
    function computeAutoresponderFieldsVisibility() {
        if (responderToggle.is(':checked')) {
            $('.stlcf-autoresponder-conditional-row').show();
        } else {
            $('.stlcf-autoresponder-conditional-row').hide();
        }
    }
    if (responderToggle.length > 0) {
        responderToggle.on('change', computeAutoresponderFieldsVisibility);
        computeAutoresponderFieldsVisibility();
    }

    // ==========================================================================
    // 4. INTERACTIVE SHORTCODE COPY ENGINE (Unified Clipboard System)
    // ==========================================================================
    var combinedCopyTriggers = $('.stlcf-copy-btn, .stlcf-list-copy-btn');
    
    if (combinedCopyTriggers.length > 0) {
        combinedCopyTriggers.on('click', function(e) {
            e.preventDefault();
            
            var shortcodeTextString = $(this).attr('data-clipboard') || $(this).attr('data-shortcode');
            if (!shortcodeTextString) {
                shortcodeTextString = $(this).siblings('.stlcf-shortcode-code').text();
            }

            var $buttonWrapperNode = $(this);
            var $dashiconGraphicElement = $buttonWrapperNode.find('.dashicons');
            
            if (shortcodeTextString) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shortcodeTextString).then(function() {
                        triggerCopySuccessFeedback($buttonWrapperNode, $dashiconGraphicElement);
                    });
                } else {
                    var $tempTransferNode = $('<input>');
                    $('body').append($tempTransferNode);
                    $tempTransferNode.val(shortcodeTextString).select();
                    document.execCommand('copy');
                    $tempTransferNode.remove();
                    triggerCopySuccessFeedback($buttonWrapperNode, $dashiconGraphicElement);
                }
            }
        });
    }

    function triggerCopySuccessFeedback($btn, $icon) {
        var originalIconClass = $icon.hasClass('dashicons-saved') ? 'dashicons-saved' : 'dashicons-admin-page';
        var activeSuccessClass = $icon.hasClass('dashicons-saved') ? 'dashicons-yes' : 'dashicons-saved';
        
        $icon.removeClass(originalIconClass).addClass(activeSuccessClass);
        var originalBackground = $btn.css('background-color');
        $btn.css('background-color', '#d1fae5');
        
        setTimeout(function() { 
            $icon.removeClass(activeSuccessClass).addClass(originalIconClass); 
            $btn.css('background-color', originalBackground);
        }, 1500);
    }
});