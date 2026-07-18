jQuery(document).ready(function($) {
    
    // 1. DYNAMIC HEARTBEAT: Nonce Refresh for Caching Bypass
    if ($('.stlcf-ajax-action-form').length > 0) {
        $('.stlcf-page-referer-url').val(window.location.href);

        $.ajax({
            url: stlcf_ajax_object.ajax_url,
            type: 'POST',
            data: { action: 'stlcf_refresh_nonce' },
            success: function(response) {
                if (response.success && response.data.token) {
                    $('.stlcf-token-field').val(response.data.token);
                }
            }
        });
    }

    // HELPER COMPONENT: Triggers cache-safe pixel fires conditionally before redirects execution
    function stlcf_fire_conversion_pixels(channel) {
        if (stlcf_ajax_object.tracking_enabled !== '1') { return; }

        var payloadMetadata = {
            content_category: 'WhatsApp Form Lead',
            submission_channel: channel,
            form_referer_url: window.location.href
        };

        // A. Meta (Facebook) Pixel Standard Event dispatch
        if (typeof fbq === 'function') {
            fbq('track', stlcf_ajax_object.fb_pixel_event, payloadMetadata);
        }

        // B. Google Analytics 4 Recommended Event dispatch
        if (typeof gtag === 'function') {
            gtag('event', 'generate_lead', {
                'method': channel,
                'page_location': window.location.href
            });
        } else if (typeof dataLayer === 'object') {
            // Fallback for custom Google Tag Manager configurations
            dataLayer.push({
                'event': 'generate_lead',
                'form_method': channel
            });
        }
    }

    // 2. ASYNC FORM INTERCEPTION DISPATCHER SYSTEM
    $('.stlcf-ajax-action-form').on('submit', function(e) {
        
        // HARDENED CACHE-PROOF EDGE: Intercept and enforce native browser validation (prevents AJAX firing if required GDPR checkbox is unchecked)
        if (!this.checkValidity()) {
            if (this.reportValidity) {
                this.reportValidity(); // Fire makkhan native alert tooltip browser warning!
            }
            return false; // Interrupt execution threads early
        }
        
        e.preventDefault();
        
        var $form = $(this);
        var $statusBox = $form.closest('.stlcf-front-wrapper').find('.stlcf-status-box');
        var $submitButtons = $form.find('.stlcf-submit-trigger');
        var submitChannel = $(document.activeElement).val() || 'whatsapp';

        $statusBox.hide().removeClass('stlcf-status-success stlcf-status-error').text('');
        $submitButtons.prop('disabled', true).css('opacity', '0.8');
        
        var originalBtnHtml = [];
        $submitButtons.each(function() {
            var $btn = $(this);
            originalBtnHtml.push({ btn: $btn, html: $btn.html() });
            var subText = (typeof stlcf_ajax_object !== 'undefined' && stlcf_ajax_object.submitting_text) ? stlcf_ajax_object.submitting_text : 'Processing...';
            $btn.html('<svg viewBox="0 0 50 50" style="width: 16px; height: 16px; animation: stlcfRotate 2s linear infinite; margin-right: 6px; vertical-align: middle; display: inline-block;"><circle cx="25" cy="25" r="20" fill="none" stroke-width="5" stroke="currentColor" stroke-linecap="round" style="stroke-dasharray: 1, 150; stroke-dashoffset: 0; animation: stlcfDash 1.5s ease-in-out infinite;"></circle></svg> ' + subText);
        });

        var formData = new FormData(this);
        formData.append('action', 'stlcf_submit_form');
        formData.append('stlcf_submit_channel', submitChannel);

        // Capture marketing UTM parameters and Referrer attribution mapping
        var searchParams = new URLSearchParams(window.location.search);
        var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        utmKeys.forEach(function(key) {
            if (searchParams.has(key)) {
                formData.append('stlcf_utm_' + key, searchParams.get(key));
            }
        });
        if (document.referrer) {
            formData.append('stlcf_referrer_url', document.referrer);
        }

        $.ajax({
            url: stlcf_ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    stlcf_fire_conversion_pixels(submitChannel);

                    if (response.data.channel === 'whatsapp') {
                        setTimeout(function() {
                            window.location.href = response.data.redirect_url;
                        }, 300);
                    } else {
                        $statusBox.addClass('stlcf-status-success')
                                  .css({ 'background': '#d1fae5', 'color': '#065f46', 'border': '1px solid #a7f3d0' })
                                  .text(response.data.message)
                                  .fadeIn();
                        $form.trigger('reset');
                    }
                } else {
                    $statusBox.addClass('stlcf-status-error')
                              .css({ 'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fca5a5' })
                              .text(response.data.message || 'An unexpected error occurred.')
                              .fadeIn();
                }
            },
            error: function() {
                $statusBox.addClass('stlcf-status-error')
                          .css({ 'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fca5a5' })
                          .text('Server communication error. Please try again.')
                          .fadeIn();
            },
            complete: function() {
                $submitButtons.prop('disabled', false).css('opacity', '1');
                if (typeof originalBtnHtml !== 'undefined' && originalBtnHtml.length > 0) {
                    originalBtnHtml.forEach(function(item) {
                        item.btn.html(item.html);
                    });
                }
            }
        });
    });

    // 3. SMART COUNTRY CODE AUTO-DETECT (Geolocation API)
    if (typeof intlTelInput !== 'undefined' && $('.stlcf-smart-phone').length > 0) {
        $('.stlcf-smart-phone').each(function() {
            var phoneInputNode = this;
            
            intlTelInput(phoneInputNode, {
                initialCountry: "auto",
                geoIpLookup: function(success, failure) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "us";
                        success(countryCode);
                    });
                },
                nationalMode: false, // Forces the input to show the full international code (e.g., +91)
                autoPlaceholder: "polite",
                utilsScript: typeof stlcf_iti_config !== 'undefined' ? stlcf_iti_config.utils_url : ""
            });

            // Minor CSS adjustment to prevent layout breaking with the new flag overlay
            $(phoneInputNode).css({
                'padding-left': '52px', // Make room for the flag
                'height': '38px'
            });
        });
    }

    // ==========================================================================
    // 4. SMART WIDGET TRIGGERS (EXIT-INTENT & TIME-DELAY)
    // ==========================================================================
    if (typeof stlcf_ajax_object !== 'undefined' && stlcf_ajax_object.fw_enabled === '1') {
        
        // Inject a dynamic bounce animation stylesheet natively
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                @keyframes stlcfAttentionBounce {
                    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                    40% { transform: translateY(-20px); }
                    60% { transform: translateY(-10px); }
                }
                .stlcf-trigger-bounce {
                    animation: stlcfAttentionBounce 1.5s ease;
                }
            `)
            .appendTo('head');

        let isWidgetTriggered = false;

        // Function to grab user's attention
        const triggerWidgetAttention = function() {
            if (isWidgetTriggered) return;
            isWidgetTriggered = true; // Ensure it only fires once per session

            // Assuming your widget wrapper has a class '.stlcf-floating-widget'
            // We apply the bounce class, and optionally force-show the tooltip
            const $widgetWrapper = $('.stlcf-floating-widget, .stlcf-fw-container'); 
            
            if ($widgetWrapper.length > 0) {
                $widgetWrapper.addClass('stlcf-trigger-bounce');
                
                // Remove the class after animation completes so it resets cleanly
                setTimeout(function() {
                    $widgetWrapper.removeClass('stlcf-trigger-bounce');
                }, 1500);
            }
        };

        // Trigger 1: Mouse leaves the top of the browser window (Exit-Intent)
        if (stlcf_ajax_object.fw_exit_intent === '1') {
            $(document).on('mouseleave', function(e) {
                // If clientY is less than 0, they moved mouse to URL bar/tabs
                if (e.clientY < 0) { 
                    triggerWidgetAttention();
                }
            });
        }

        // Trigger 2: Time Delay
        const delaySeconds = parseInt(stlcf_ajax_object.fw_time_delay, 10);
        if (delaySeconds > 0) {
            setTimeout(triggerWidgetAttention, delaySeconds * 1000);
        }
    }

    // 5. MULTI-AGENT FLOATING PANEL TOGGLE
    $(document).on('click', '.stlcf-multi-agent-trigger', function(e) {
        e.preventDefault();
        var $panel = $(this).siblings('.stlcf-multi-agent-panel');
        if ($panel.length > 0) {
            $panel.slideToggle(200);
        }
    });

    $(document).on('click', '.stlcf-close-agent-panel-btn', function(e) {
        e.preventDefault();
        $(this).closest('.stlcf-multi-agent-panel').fadeOut(150);
    });

    // 6. FRONTEND CONDITIONAL FORM LOGIC ENGINE
    function evaluateFormConditionalLogic($form) {
        $form.find('.stlcf-cond-logic-field').each(function() {
            var $group = $(this);
            var targetFieldKey = $group.attr('data-cond-field');
            var operator = $group.attr('data-cond-operator');
            var matchValue = ($group.attr('data-cond-value') || '').trim().toLowerCase();

            var $targetInput = $form.find('[data-field-key="' + targetFieldKey + '"]');
            if ($targetInput.length > 0) {
                var rawValue = $targetInput.val() || '';
                
                if ($targetInput.is('select') && rawValue.indexOf('|') !== -1) {
                    rawValue = rawValue.split('|')[0];
                }
                
                var currentValue = rawValue.trim().toLowerCase();
                var isMatch = false;

                if (operator === 'equals') {
                    isMatch = (currentValue === matchValue);
                } else if (operator === 'not_equals') {
                    isMatch = (currentValue !== matchValue && currentValue !== '');
                }

                var $inputInsideGroup = $group.find('input, textarea, select');

                if (isMatch) {
                    $group.slideDown(200);
                    if ($inputInsideGroup.attr('data-was-required') === 'true') {
                        $inputInsideGroup.attr('required', 'required').attr('aria-required', 'true');
                    }
                } else {
                    $group.slideUp(150);
                    if ($inputInsideGroup.attr('required')) {
                        $inputInsideGroup.attr('data-was-required', 'true');
                        $inputInsideGroup.removeAttr('required').removeAttr('aria-required');
                    }
                }
            }
        });
    }

    $('.stlcf-ajax-action-form').each(function() {
        var $form = $(this);
        
        $form.find('input[required], textarea[required], select[required]').each(function() {
            $(this).attr('data-was-required', 'true');
        });

        evaluateFormConditionalLogic($form);
        
        $form.on('change input keyup', 'input, textarea, select', function() {
            evaluateFormConditionalLogic($form);
        });
    });

    // 7. CONVERSATIONAL MULTISTEP FORM FLOW ENGINE
    function initializeConversationalForms() {
        $('.stlcf-ajax-action-form[data-layout="conversational"]').each(function() {
            var $form = $(this);
            if ($form.hasClass('stlcf-conversational-initialized')) { return; }
            $form.addClass('stlcf-conversational-initialized');

            var $submitWrap = $form.find('.stlcf-submit-trigger').closest('div');
            $submitWrap.addClass('stlcf-conversational-submit-wrap').hide();

            function refreshSteps() {
                var $visibleGroups = $form.find('.stlcf-field-group').filter(function() {
                    return $(this).css('display') !== 'none';
                });
                var activeIndex = $form.data('stlcf-active-step') || 0;

                if (activeIndex >= $visibleGroups.length) {
                    activeIndex = $visibleGroups.length - 1;
                }
                if (activeIndex < 0) { activeIndex = 0; }
                $form.data('stlcf-active-step', activeIndex);

                // Progress Bar Indicator
                var $progressContainer = $form.find('.stlcf-conversational-progress-container');
                if ($progressContainer.length === 0) {
                    $progressContainer = $('<div class="stlcf-conversational-progress-container" style="width:100%; height:6px; background:#e2e8f0; border-radius:3px; margin-bottom:20px; overflow:hidden; display:flex;"><div class="stlcf-conversational-progress-bar" style="height:100%; width:0%; transition:width-ease 0.3s; background:#3b82f6;"></div></div>');
                    $form.prepend($progressContainer);
                }
                var progressPercent = $visibleGroups.length > 0 ? Math.round( ( activeIndex / $visibleGroups.length ) * 100 ) : 0;
                if (activeIndex === $visibleGroups.length - 1) {
                    progressPercent = 100;
                }
                $progressContainer.find('.stlcf-conversational-progress-bar').css('width', progressPercent + '%');

                $visibleGroups.each(function(index) {
                    var $group = $(this);
                    $group.find('.stlcf-conversational-nav').remove();

                    if (index === activeIndex) {
                        $group.show();
                        var navHtml = '<div class="stlcf-conversational-nav" style="display:flex; gap:10px; margin-top:15px; width:100%;">';
                        if (index > 0) {
                            navHtml += '<button type="button" class="button stlcf-conv-back-btn" style="background:#e2e8f0; color:#1e293b; border:none; padding:10px 20px; border-radius:6px; font-weight:600; cursor:pointer;">Back</button>';
                        }
                        if (index < $visibleGroups.length - 1) {
                            navHtml += '<button type="button" class="button stlcf-conv-next-btn" style="background:#3b82f6; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:600; cursor:pointer; flex:1;">Next</button>';
                        }
                        navHtml += '</div>';
                        $group.append(navHtml);
                    } else {
                        $group.hide();
                    }
                });

                if (activeIndex === $visibleGroups.length - 1) {
                    $submitWrap.show();
                } else {
                    $submitWrap.hide();
                }
            }

            $form.on('click', '.stlcf-conv-next-btn', function(e) {
                e.preventDefault();
                var activeIndex = $form.data('stlcf-active-step') || 0;
                var $visibleGroups = $form.find('.stlcf-field-group').filter(function() {
                    return $(this).css('display') !== 'none';
                });
                var $currentGroup = $visibleGroups.eq(activeIndex);
                var isValid = true;
                
                $currentGroup.find('input, textarea, select').each(function() {
                    if (!this.checkValidity()) {
                        if (this.reportValidity) { this.reportValidity(); }
                        isValid = false;
                        return false;
                    }
                });

                if (isValid) {
                    $form.data('stlcf-active-step', activeIndex + 1);
                    refreshSteps();
                }
            });

            $form.on('click', '.stlcf-conv-back-btn', function(e) {
                e.preventDefault();
                var activeIndex = $form.data('stlcf-active-step') || 0;
                $form.data('stlcf-active-step', activeIndex - 1);
                refreshSteps();
            });

            $form.on('change input keyup', 'input, textarea, select', function() {
                setTimeout(refreshSteps, 220);
            });

            refreshSteps();
        });
    }

    initializeConversationalForms();

    // 8. DIGITAL SIGNATURE PAD CANVAS ENGINE
    $('.stlcf-signature-canvas').each(function() {
        var canvas = this;
        var ctx = canvas.getContext('2d');
        var drawing = false;
        var mousePos = { x:0, y:0 };
        var lastPos = mousePos;
        var $hiddenInput = $(canvas).siblings('input[type="hidden"]');

        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';

        function getMousePos(canvasDom, touchOrMouseEvent) {
            var rect = canvasDom.getBoundingClientRect();
            var clientX = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientX : touchOrMouseEvent.clientX;
            var clientY = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientY : touchOrMouseEvent.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            drawing = true;
            lastPos = getMousePos(canvas, e);
        }

        function draw(e) {
            if (!drawing) return;
            e.preventDefault();
            mousePos = getMousePos(canvas, e);
            ctx.beginPath();
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(mousePos.x, mousePos.y);
            ctx.stroke();
            lastPos = mousePos;
            
            // Save base64 state to input immediately
            $hiddenInput.val(canvas.toDataURL('image/png'));
        }

        function stopDrawing() {
            drawing = false;
        }

        // Mouse listeners
        canvas.addEventListener('mousedown', startDrawing, false);
        canvas.addEventListener('mousemove', draw, false);
        window.addEventListener('mouseup', stopDrawing, false);

        // Touch listeners (Mobile devices support)
        canvas.addEventListener('touchstart', function(e) {
            startDrawing(e);
        }, false);
        canvas.addEventListener('touchmove', function(e) {
            draw(e);
        }, false);
        canvas.addEventListener('touchend', stopDrawing, false);
    });

    // Clear Signature functionality
    $(document).on('click', '.stlcf-clear-sig-btn', function() {
        var canvasId = $(this).attr('data-canvas');
        var canvas = document.getElementById(canvasId);
        if (canvas) {
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            $(canvas).siblings('input[type="hidden"]').val('');
        }
    });

    // 9. LEAD AUTO-RECOVERY (ABANDONED LEADS LOGGER)
    var abandonedTimer;
    $(document).on('input change keyup', '.stlcf-ajax-action-form input, .stlcf-ajax-action-form textarea, .stlcf-ajax-action-form select', function() {
        var $form = $(this).closest('.stlcf-ajax-action-form');
        var formId = $form.find('input[name="form_id"]').val();
        var nonce = $form.find('.stlcf-token-field').val();
        
        if (!formId || !nonce) return;

        clearTimeout(abandonedTimer);
        abandonedTimer = setTimeout(function() {
            var formData = {};
            
            $form.find('input, textarea, select').each(function() {
                var name = $(this).attr('name');
                if (name && name.indexOf('stlcf_input[') === 0) {
                    var lbl = name.substring(12, name.length - 1);
                    var val = $(this).val();
                    if (val && val.indexOf('data:image/') !== 0) {
                        formData[lbl] = val;
                    }
                }
            });

            if ($.isEmptyObject(formData)) return;

            var searchParams = new URLSearchParams(window.location.search);
            var utmObj = {};
            ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(key) {
                if (searchParams.has(key)) {
                    utmObj[key] = searchParams.get(key);
                }
            });

            $.ajax({
                url: stlcf_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'stlcf_log_abandoned_lead',
                    form_id: formId,
                    stlcf_nonce: nonce,
                    stlcf_input: formData,
                    page_url: window.location.href,
                    utm_data: utmObj,
                    referrer_url: document.referrer || ''
                },
                success: function(res) {
                    // Silent background log
                }
            });
        }, 3000);
    });
});