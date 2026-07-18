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
            placeholder: 'ui-state-highlight',
            stop: function() {
                updateConditionalFieldDropdowns();
            }
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
                    '<option value="file">Secure File Upload</option>' +
                    '<option value="signature">Digital Signature Pad</option>' +
                    agentOptionMarkup +
                '</select>' +
                '<input type="text" class="stlcf-field-label-input" name="stlcf_fields[' + rowIndex + '][label]" placeholder="Custom Field Label" required>' +
                '<label><input type="checkbox" name="stlcf_fields[' + rowIndex + '][required]" value="1"> Required</label>' +
                '<button type="button" class="button remove-field-row">Delete</button>' +
                '<div class="stlcf-routing-config stlcf-hide-field">' +
                    '<p class="description">Define Agents Distribution configuration map (One entry per line):</p>' +
                    '<textarea name="stlcf_fields[' + rowIndex + '][routing]" placeholder="Format: Name|Phone (With country code, no spaces)\nExample:\nSales Team|919999999999\nSupport Desk|918888888888"></textarea>' +
                '</div>' +
                '<div class="stlcf-conditional-logic-trigger-wrapper" style="width:100%; margin-top:8px;">' +
                    '<label style="font-size:11px; font-weight:normal; display:inline-flex; align-items:center; gap:4px; user-select:none;">' +
                        '<input type="checkbox" class="stlcf-cond-toggle" name="stlcf_fields[' + rowIndex + '][cond_enabled]" value="1">' +
                        '<strong>Enable Conditional Logic rules</strong>' +
                    '</label>' +
                '</div>' +
                '<div class="stlcf-conditional-logic-rules-panel stlcf-hide-field" style="width:100%; margin-top:8px; padding:10px; background:#fff; border:1px solid #e2e8f0; border-radius:4px;">' +
                    '<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">' +
                        '<span style="font-size:12px; font-weight:600;">Show this field if:</span>' +
                        '<select class="stlcf-cond-field-select" name="stlcf_fields[' + rowIndex + '][cond_field]" style="min-width:150px; font-size:12px; height:30px;">' +
                            '<option value="">Select Target Field</option>' +
                        '</select>' +
                        '<select name="stlcf_fields[' + rowIndex + '][cond_operator]" style="min-width:100px; font-size:12px; height:30px;">' +
                            '<option value="equals">is equal to</option>' +
                            '<option value="not_equals">is not equal to</option>' +
                        '</select>' +
                        '<input type="text" name="stlcf_fields[' + rowIndex + '][cond_value]" placeholder="Matching value..." style="flex:1; min-width:120px; font-size:12px; height:30px; padding:0 8px;">' +
                    '</div>' +
                '</div>' +
            '</div>';
            
            $('#stlcf-fields-container').append(newRow);
            rowIndex++;
            updateConditionalFieldDropdowns();
        });
        
        $(document).on('click', '.remove-field-row', function() {
            if ($('#stlcf-fields-container .stlcf-field-row').length > 1) { 
                $(this).closest('.stlcf-field-row').remove(); 
                updateConditionalFieldDropdowns();
            } else { 
                alert('Your form must contain at least one input field row.'); 
            }
        });

        $(document).on('change', '#stlcf-fields-container select', function() {
            var selectedType = $(this).val();

            if ((selectedType === 'file' || selectedType === 'signature') && (!window.stlcf_admin_vars || window.stlcf_admin_vars.is_pro !== 1)) {
                alert('🔒 Pro Feature Only: Secure File Uploads and Digital Signature Pad fields are premium features. Please activate a Pro license key in Settings -> Go Pro to unlock them!');
                $(this).val('text').trigger('change');
                return;
            }

            var $routingBlock = $(this).closest('.stlcf-field-row').find('.stlcf-routing-config');
            if (selectedType === 'agent_select') {
                $routingBlock.removeClass('stlcf-hide-field');
            } else {
                $routingBlock.addClass('stlcf-hide-field');
            }
        });

        // 1B. CONDITIONAL LOGIC UTILITIES
        function updateConditionalFieldDropdowns() {
            var fields = [];
            $('#stlcf-fields-container .stlcf-field-row').each(function() {
                var labelInput = $(this).find('input[type="text"][name*="[label]"]').val();
                if (labelInput) {
                    var sanitizedLabel = labelInput.trim();
                    var fieldKey = sanitizedLabel.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    fields.push({ key: fieldKey, label: sanitizedLabel });
                }
            });

            $('.stlcf-cond-field-select').each(function() {
                var $select = $(this);
                var selectedValue = $select.attr('data-selected') || $select.val();
                var currentFieldRowLabel = $select.closest('.stlcf-field-row').find('input[type="text"][name*="[label]"]').val() || '';
                var currentFieldKey = currentFieldRowLabel.trim().toLowerCase().replace(/[^a-z0-9]/g, '_');

                $select.html('<option value="">Select Target Field</option>');
                fields.forEach(function(field) {
                    if (field.key !== currentFieldKey) {
                        var isSelected = (field.key === selectedValue) ? ' selected' : '';
                        $select.append('<option value="' + field.key + '"' + isSelected + '>' + field.label + '</option>');
                    }
                });
            });
        }

        // Initialize and listen to changes on labels
        updateConditionalFieldDropdowns();
        $(document).on('keyup change', 'input[name*="[label]"]', function() {
            updateConditionalFieldDropdowns();
        });

        $(document).on('change', '.stlcf-cond-toggle', function() {
            var $panel = $(this).closest('.stlcf-field-row').find('.stlcf-conditional-logic-rules-panel');
            if ($(this).is(':checked')) {
                $panel.removeClass('stlcf-hide-field');
            } else {
                $panel.addClass('stlcf-hide-field');
            }
        });
    }

    // ==========================================================================
    // 2. DASHBOARD SETTINGS ENGINE (Tabs Navigation System)
    // ==========================================================================
    $('.stlcf-nav-tab, .stlcf-settings-sidebar-tab').on('click', function(e) {
        e.preventDefault();
        var targetSectionId = $(this).attr('data-tab');
        if (targetSectionId) {
            $('.stlcf-nav-tab, .stlcf-settings-sidebar-tab').removeClass('nav-tab-active active');
            $('.stlcf-tab-section').addClass('stlcf-hide-tab').hide();
            
            $(this).addClass($(this).hasClass('stlcf-nav-tab') ? 'nav-tab-active' : 'active');
            $('#' + targetSectionId).removeClass('stlcf-hide-tab').show();
            
            var hrefVal = $(this).attr('href');
            if (hrefVal) {
                history.pushState(null, null, hrefVal);
            }
        }
    });

    var activeHash = window.location.hash;
    if (activeHash) {
        var correspondingTab = $('.stlcf-nav-tab[href="' + activeHash + '"], .stlcf-settings-sidebar-tab[href="' + activeHash + '"]');
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
    var multiAgentToggle = $('#stlcf_enable_multi_agent');
    function computeWidgetFieldsVisibility() {
        if (widgetMasterToggle.is(':checked')) {
            $('.stlcf-widget-conditional-row').show();
            computeMultiAgentFieldsVisibility();
        } else {
            $('.stlcf-widget-conditional-row').hide();
            $('.stlcf-multi-agent-conditional-row').hide();
        }
    }
    function computeMultiAgentFieldsVisibility() {
        if (widgetMasterToggle.is(':checked') && multiAgentToggle.is(':checked')) {
            $('.stlcf-multi-agent-conditional-row').show();
        } else {
            $('.stlcf-multi-agent-conditional-row').hide();
        }
    }
    if (widgetMasterToggle.length > 0) {
        widgetMasterToggle.on('change', computeWidgetFieldsVisibility);
        multiAgentToggle.on('change', computeMultiAgentFieldsVisibility);
        computeWidgetFieldsVisibility();
    }

    // Dynamic Multi-Agent Repeater Interface
    var agentRowIndex = $('#stlcf-agents-repeater-container .stlcf-agent-repeater-row').length;
    $('#stlcf-add-agent-btn').on('click', function(e) {
        e.preventDefault();
        var newAgentRow = '<div class="stlcf-agent-repeater-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; margin-bottom:10px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">' +
            '<input type="text" name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][name]" placeholder="Agent Name" required style="flex:1; min-width:120px;">' +
            '<input type="text" name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][title]" placeholder="Role / Department" style="flex:1; min-width:120px;">' +
            '<input type="text" name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][phone]" placeholder="Phone Number" required style="flex:1; min-width:150px;">' +
            '<select name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][status]" style="flex:1; min-width:100px;">' +
                '<option value="online">Online</option>' +
                '<option value="away">Away</option>' +
                '<option value="offline">Offline</option>' +
            '</select>' +
            '<input type="text" class="stlcf-agent-avatar-url" name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][avatar]" placeholder="Avatar URL" style="flex:1.5; min-width:150px;">' +
            '<input type="text" name="stlcf_general_settings[multi_agents_list][' + agentRowIndex + '][allowed_countries]" placeholder="Allowed Countries (e.g. US,CA)" style="flex:1; min-width:120px;">' +
            '<button type="button" class="button stlcf-remove-agent-btn" style="border-color:#dc2626; color:#dc2626;">Delete</button>' +
        '</div>';
        $('#stlcf-agents-repeater-container').append(newAgentRow);
        agentRowIndex++;
    });

    $(document).on('click', '.stlcf-remove-agent-btn', function(e) {
        e.preventDefault();
        $(this).closest('.stlcf-agent-repeater-row').remove();
    });

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

    // G. Webhooks Customizer Toggle
    var webhookToggle = $('#stlcf_enable_webhook');
    function computeWebhookVisibility() {
        if (webhookToggle.is(':checked')) {
            $('.stlcf-webhook-conditional-row').fadeIn(150);
        } else {
            $('.stlcf-webhook-conditional-row').hide();
        }
    }
    if (webhookToggle.length > 0) {
        webhookToggle.on('change', computeWebhookVisibility);
        computeWebhookVisibility();
    }

    // H. Integrations Customizer Toggles
    var mailchimpToggle = $('#stlcf_enable_mailchimp');
    function computeMailchimpVisibility() {
        if (mailchimpToggle.is(':checked')) {
            $('.stlcf-mailchimp-row').fadeIn(150);
        } else {
            $('.stlcf-mailchimp-row').hide();
        }
    }
    if (mailchimpToggle.length > 0) {
        mailchimpToggle.on('change', computeMailchimpVisibility);
        computeMailchimpVisibility();
    }

    var hubspotToggle = $('#stlcf_enable_hubspot');
    function computeHubspotVisibility() {
        if (hubspotToggle.is(':checked')) {
            $('.stlcf-hubspot-row').fadeIn(150);
        } else {
            $('.stlcf-hubspot-row').hide();
        }
    }
    if (hubspotToggle.length > 0) {
        hubspotToggle.on('change', computeHubspotVisibility);
        computeHubspotVisibility();
    }

    // GDPR Cron Settings Controller
    var gdprCronToggle = $('#stlcf_enable_gdpr_cron');
    function computeGdprCronVisibility() {
        if (gdprCronToggle.is(':checked')) {
            $('.stlcf-gdpr-cron-conditional-row').fadeIn(150);
        } else {
            $('.stlcf-gdpr-cron-conditional-row').hide();
        }
    }
    if (gdprCronToggle.length > 0) {
        gdprCronToggle.on('change', computeGdprCronVisibility);
        computeGdprCronVisibility(); // Execute on load
    }

    // One-Click Presets Importer
    $(document).on('click', '.stlcf-preset-import-btn', function() {
        var preset = $(this).attr('data-preset');
        var isAgentEnabled = $('#stlcf-fields-container').attr('data-agent-routing');
        
        // Confirm before clearing fields
        if ($('#stlcf-fields-container .stlcf-field-row').length > 0) {
            if (!confirm('Are you sure you want to clear current fields and import this preset?')) {
                return;
            }
        }
        
        // Clear current container
        $('#stlcf-fields-container').html('');
        rowIndex = 0;
        
        var fieldsToInsert = [];
        if (preset === 'contact') {
            fieldsToInsert = [
                { type: 'text', label: 'Your Name', req: 1 },
                { type: 'email', label: 'Email Address', req: 1 },
                { type: 'textarea', label: 'Brief Message', req: 1 }
            ];
        } else if (preset === 'routing') {
            fieldsToInsert = [
                { type: 'text', label: 'Full Name', req: 1 },
                { type: 'agent_select', label: 'Department Name', req: 1, routing: "Sales Team|919999999999\nSupport Desk|918888888888" },
                { type: 'textarea', label: 'Explain Inquiry', req: 1 }
            ];
        } else if (preset === 'upload') {
            fieldsToInsert = [
                { type: 'text', label: 'Full Name', req: 1 },
                { type: 'file', label: 'Upload CV / Document', req: 1 },
                { type: 'textarea', label: 'Cover Note', req: 0 }
            ];
        }
        
        fieldsToInsert.forEach(function(f) {
            var agentOptionMarkup = isAgentEnabled === '1' ? '<option value="agent_select">Agent Dropdown Routing</option>' : '';
            var routingHideClass = (f.type === 'agent_select') ? '' : 'stlcf-hide-field';
            var checkedReq = f.req ? ' checked' : '';
            var routingText = f.routing || '';
            
            var rowHtml = '<div class="stlcf-field-row">' +
                '<span class="dashicons dashicons-menu"></span>' +
                '<select name="stlcf_fields[' + rowIndex + '][type]">' +
                    '<option value="text" ' + (f.type === 'text' ? 'selected' : '') + '>Text Field</option>' +
                    '<option value="email" ' + (f.type === 'email' ? 'selected' : '') + '>Email Field</option>' +
                    '<option value="textarea" ' + (f.type === 'textarea' ? 'selected' : '') + '>Textarea</option>' +
                    '<option value="number" ' + (f.type === 'number' ? 'selected' : '') + '>Number</option>' +
                    '<option value="file" ' + (f.type === 'file' ? 'selected' : '') + '>Secure File Upload</option>' +
                    '<option value="signature" ' + (f.type === 'signature' ? 'selected' : '') + '>Digital Signature Pad</option>' +
                    (isAgentEnabled === '1' ? '<option value="agent_select" ' + (f.type === 'agent_select' ? 'selected' : '') + '>Agent Dropdown Routing</option>' : '') +
                '</select>' +
                '<input type="text" class="stlcf-field-label-input" name="stlcf_fields[' + rowIndex + '][label]" value="' + f.label + '" placeholder="Custom Field Label" required>' +
                '<label><input type="checkbox" name="stlcf_fields[' + rowIndex + '][required]" value="1"' + checkedReq + '> Required</label>' +
                '<button type="button" class="button remove-field-row">Delete</button>' +
                '<div class="stlcf-routing-config ' + routingHideClass + '">' +
                    '<p class="description">Define Agents Distribution configuration map (One entry per line):</p>' +
                    '<textarea name="stlcf_fields[' + rowIndex + '][routing]" placeholder="Format: Name|Phone\nExample: Sales Team|919999999999">' + routingText + '</textarea>' +
                '</div>' +
                '<div class="stlcf-conditional-logic-trigger-wrapper" style="width:100%; margin-top:8px;">' +
                    '<label style="font-size:11px; font-weight:normal; display:inline-flex; align-items:center; gap:4px; user-select:none;">' +
                        '<input type="checkbox" class="stlcf-cond-toggle" name="stlcf_fields[' + rowIndex + '][cond_enabled]" value="1">' +
                        '<strong>Enable Conditional Logic rules</strong>' +
                    '</label>' +
                    '<div class="stlcf-conditional-logic-rules-panel stlcf-hide-field" style="margin-top:5px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">' +
                        '<span>Show this field if</span>' +
                        '<select class="stlcf-cond-field-select" name="stlcf_fields[' + rowIndex + '][cond_field]" data-selected=""></select>' +
                        '<select name="stlcf_fields[' + rowIndex + '][cond_operator]">' +
                            '<option value="equals">is equal to</option>' +
                            '<option value="not_equals">is not equal to</option>' +
                        '</select>' +
                        '<input type="text" name="stlcf_fields[' + rowIndex + '][cond_value]" value="" placeholder="Matching value...">' +
                    '</div>' +
                '</div>' +
            '</div>';
            
            $('#stlcf-fields-container').append(rowHtml);
            rowIndex++;
        });
        
        updateConditionalFieldDropdowns();
    });
});