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
        $submitButtons.prop('disabled', true).css('opacity', '0.6');

        var formData = $form.serializeArray();
        formData.push({ name: 'action', value: 'stlcf_submit_form' });
        formData.push({ name: 'stlcf_submit_channel', value: submitChannel });

        $.ajax({
            url: stlcf_ajax_object.ajax_url,
            type: 'POST',
            data: formData,
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
            }
        });
    });
});