<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class STLCF_Frontend {

    private $form_rendered = false;

    public function __construct() {
        // Enforce secure prefixed shortcode deployment
        add_shortcode( 'stlcf_chat_form', array( $this, 'render_dynamic_form' ) );
        add_action( 'wp_footer', array( $this, 'render_floating_widget' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

        add_action( 'wp_ajax_stlcf_refresh_nonce', array( $this, 'handle_nonce_refresh' ) );
        add_action( 'wp_ajax_nopriv_stlcf_refresh_nonce', array( $this, 'handle_nonce_refresh' ) );
        add_action( 'wp_ajax_stlcf_submit_form', array( $this, 'handle_ajax_form_submission' ) );
        add_action( 'wp_ajax_nopriv_stlcf_submit_form', array( $this, 'handle_ajax_form_submission' ) );

        add_action( 'wp_head', array( $this, 'inject_baseline_pixels_code' ), 5 );

        add_filter( 'wpseo_schema_graph_pieces', array( $this, 'yoast_inject_schema_piece' ), 11, 2 );
        add_filter( 'rank_math/json_ld', array( $this, 'rank_math_inject_schema' ), 11, 2 );
        add_action( 'wp_footer', array( $this, 'inject_fallback_standalone_schema' ), 99 );
    }

    public function enqueue_frontend_scripts() {
        wp_enqueue_style( 'stlcf-public-style', STLCF_PLUGIN_URL . 'assets/public/css/stlcf-public.css', array(), STLCF_VERSION );
        wp_enqueue_script( 'stlcf-public-script', STLCF_PLUGIN_URL . 'assets/public/js/stlcf-public.js', array( 'jquery' ), STLCF_VERSION, true );

        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        
        // Pass essential variables to our JavaScript engine
        wp_localize_script( 'stlcf-public-script', 'stlcf_ajax_object', array(
            'ajax_url'         => admin_url( 'admin-ajax.php' ),
            'tracking_enabled' => isset( $stlcf_g_settings['enable_pixels_tracking'] ) ? sanitize_text_field( $stlcf_g_settings['enable_pixels_tracking'] ) : '0',
            'fb_pixel_event'   => isset( $stlcf_g_settings['fb_pixel_event'] ) ? sanitize_text_field( $stlcf_g_settings['fb_pixel_event'] ) : 'Lead',
            'ga4_id'           => isset( $stlcf_g_settings['ga4_measurement_id'] ) ? sanitize_text_field( $stlcf_g_settings['ga4_measurement_id'] ) : '',
            // New Smart Triggers Data
            'fw_enabled'       => isset( $stlcf_g_settings['floating_btn'] ) ? $stlcf_g_settings['floating_btn'] : '0',
            'fw_exit_intent'   => isset( $stlcf_g_settings['fw_exit_intent'] ) ? $stlcf_g_settings['fw_exit_intent'] : '0',
            'fw_time_delay'    => isset( $stlcf_g_settings['fw_time_delay'] ) ? intval( $stlcf_g_settings['fw_time_delay'] ) : 0
        ) );

        // Enqueue Smart Country Code Library if enabled
        $stlcf_geo_enabled = isset( $stlcf_g_settings['enable_geo_phone'] ) ? $stlcf_g_settings['enable_geo_phone'] : '1';
        if ( $stlcf_geo_enabled === '1' ) {
            wp_enqueue_style( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css', array(), '17.0.8' );
            wp_enqueue_script( 'intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js', array( 'jquery' ), '17.0.8', true );
            
            // Pass the utils script URL to our local JS object for formatting
            wp_localize_script( 'stlcf-public-script', 'stlcf_iti_config', array(
                'utils_url' => 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js'
            ));
        }

        $stlcf_c_type = isset( $stlcf_g_settings['captcha_type'] ) ? $stlcf_g_settings['captcha_type'] : 'none';
        if ( $stlcf_c_type === 'turnstile' ) {
            // phpcs:ignore PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent, WordPress.WP.EnqueuedResourceParameters.MissingVersion
            wp_enqueue_script( 'stlcf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, true );
        } elseif ( $stlcf_c_type === 'recaptcha' ) {
            // phpcs:ignore PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent, WordPress.WP.EnqueuedResourceParameters.MissingVersion
            wp_enqueue_script( 'stlcf-recaptcha-v2', 'https://www.google.com/recaptcha/api.js', array(), null, true );
        } elseif ( $stlcf_c_type === 'recaptcha_v3' ) {
            $stlcf_v3_key = isset( $stlcf_g_settings['recaptcha_v3_site_key'] ) ? sanitize_text_field( $stlcf_g_settings['recaptcha_v3_site_key'] ) : '';
            if ( ! empty( $stlcf_v3_key ) ) {
                // phpcs:ignore PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent, WordPress.WP.EnqueuedResourceParameters.MissingVersion
                wp_enqueue_script( 'stlcf-recaptcha-v3', 'https://www.google.com/recaptcha/api.js?render=' . urlencode( $stlcf_v3_key ), array( 'jquery' ), null, true );
            }
        }
    }

    public function inject_baseline_pixels_code() {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        if ( ! isset( $stlcf_g_settings['enable_pixels_tracking'] ) || $stlcf_g_settings['enable_pixels_tracking'] !== '1' ) { return; }
        if ( ! isset( $stlcf_g_settings['inject_base_scripts'] ) || $stlcf_g_settings['inject_base_scripts'] !== '1' ) { return; }

        $fb_id  = isset( $stlcf_g_settings['fb_pixel_id'] ) ? sanitize_text_field( $stlcf_g_settings['fb_pixel_id'] ) : '';
        $ga4_id = isset( $stlcf_g_settings['ga4_measurement_id'] ) ? sanitize_text_field( $stlcf_g_settings['ga4_measurement_id'] ) : '';

        // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
        if ( ! empty( $fb_id ) ) {
            ?>
            <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo esc_js( $fb_id ); ?>');
            fbq('track', 'PageView');
            </script>
            <?php
        }

        if ( ! empty( $ga4_id ) ) {
            ?>
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              window.gtag = gtag;
              gtag('js', new Date());
              gtag('config', '<?php echo esc_js( $ga4_id ); ?>');
            </script>
            <?php
        }
        // phpcs:enable
    }

    public function render_floating_widget() {
        $stlcf_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_float_btn = isset( $stlcf_settings['floating_btn'] ) ? $stlcf_settings['floating_btn'] : '0';
        $stlcf_global_ph = isset( $stlcf_settings['global_phone'] ) ? preg_replace( '/[^0-9]/', '', $stlcf_settings['global_phone'] ) : '';
        $stlcf_offline_action = isset( $stlcf_settings['offline_action'] ) ? $stlcf_settings['offline_action'] : 'show_notice';

        if ( $stlcf_float_btn !== '1' || empty( $stlcf_global_ph ) || ( $this->is_currently_offline() && $stlcf_offline_action === 'hide_widget' ) ) { 
            return; 
        }

        $stlcf_fw_vis = isset( $stlcf_settings['fw_visibility'] ) ? $stlcf_settings['fw_visibility'] : 'sitewide';
        if ( $stlcf_fw_vis === 'homepage' && ! is_front_page() ) { return; }
        if ( $stlcf_fw_vis === 'singular' && ! is_singular() ) { return; }

        $stlcf_fl_clr   = isset( $stlcf_settings['float_btn_color'] ) ? sanitize_hex_color( $stlcf_settings['float_btn_color'] ) : '#25D366';
        $stlcf_fw_pos   = isset( $stlcf_settings['fw_position'] ) ? sanitize_key( $stlcf_settings['fw_position'] ) : 'right';
        $stlcf_fw_txt   = isset( $stlcf_settings['fw_tooltip_text'] ) ? sanitize_text_field( $stlcf_settings['fw_tooltip_text'] ) : '';
        $stlcf_fw_msg   = isset( $stlcf_settings['fw_prefilled_msg'] ) ? sanitize_text_field( $stlcf_settings['fw_prefilled_msg'] ) : '';

        $stlcf_wa_url = "https://wa.me/" . esc_attr( $stlcf_global_ph );
        if ( ! empty( $stlcf_fw_msg ) ) {
            $stlcf_wa_url .= "?text=" . rawurlencode( $stlcf_fw_msg );
        }

        $stlcf_geom_pos = ( $stlcf_fw_pos === 'left' ) ? 'left:30px;' : 'right:30px;';
        $stlcf_tooltip_geom = ( $stlcf_fw_pos === 'left' ) ? 'left: 70px;' : 'right: 70px;';
        ?>
        <div class="stlcf-floating-container stlcf-floating-widget" style="position:fixed; bottom:30px; <?php echo esc_attr( $stlcf_geom_pos ); ?> z-index:999999; display:flex; align-items:center;">
            <?php if ( ! empty( $stlcf_fw_txt ) ) : ?>
                <div class="stlcf-fw-tooltip" style="position:absolute; bottom:15px; <?php echo esc_attr( $stlcf_tooltip_geom ); ?> background:#1e293b; color:#fff; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:500; white-space:nowrap; box-shadow:0 2px 8px rgba(0,0,0,0.15); pointer-events:none; font-family: inherit;">
                    <?php echo esc_html( $stlcf_fw_txt ); ?>
                </div>
            <?php endif; ?>

            <a href="<?php echo esc_url( $stlcf_wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="stlcf-floating-bubble-link" style="width:60px; height:60px; background-color:<?php echo esc_attr( $stlcf_fl_clr ); ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(0,0,0,0.25); text-decoration:none; transition:transform 0.2s ease-in-out;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width:30px; height:30px; fill:#ffffff;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
            </a>
        </div>
        <?php
    }

    public function render_dynamic_form( $atts ) {
        $this->form_rendered = true;

        $stlcf_attrs = shortcode_atts( array( 'id' => '' ), $atts, 'stlcf_chat_form' );
        $stlcf_form_id = intval( $stlcf_attrs['id'] );
        if ( empty( $stlcf_form_id ) ) { return ''; }

        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        $stlcf_form_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_form_id ) );
        // phpcs:enable

        if ( ! $stlcf_form_data ) { return ''; }

        $stlcf_fields = maybe_unserialize( $stlcf_form_data->fields );
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        
        $stlcf_f_size    = isset( $stlcf_g_settings['font_size'] ) ? intval( $stlcf_g_settings['font_size'] ) : 16;
        $stlcf_b_design  = isset( $stlcf_g_settings['button_design'] ) ? $stlcf_g_settings['button_design'] : 'style_flat';
        $stlcf_wa_txt    = isset( $stlcf_g_settings['wa_btn_text'] ) && ! empty( $stlcf_g_settings['wa_btn_text'] ) ? $stlcf_g_settings['wa_btn_text'] : 'Submit via WhatsApp';
        $stlcf_em_btn_en = isset( $stlcf_g_settings['enable_email_btn'] ) ? $stlcf_g_settings['enable_email_btn'] : '0';
        $stlcf_em_txt    = isset( $stlcf_g_settings['email_btn_text'] ) && ! empty( $stlcf_g_settings['email_btn_text'] ) ? $stlcf_g_settings['email_btn_text'] : 'Submit via Email';

        $stlcf_wa_clr    = isset( $stlcf_g_settings['btn_color'] ) ? sanitize_hex_color( $stlcf_g_settings['btn_color'] ) : '#25D366';
        $stlcf_em_clr    = isset( $stlcf_g_settings['email_btn_color'] ) ? sanitize_hex_color( $stlcf_g_settings['email_btn_color'] ) : '#1e293b';
        $stlcf_c_type    = isset( $stlcf_g_settings['captcha_type'] ) ? $stlcf_g_settings['captcha_type'] : 'none';
        $stlcf_ts_key    = isset( $stlcf_g_settings['turnstile_site_key'] ) ? sanitize_text_field($stlcf_g_settings['turnstile_site_key']) : '';
        $stlcf_rc_key    = isset( $stlcf_g_settings['recaptcha_site_key'] ) ? sanitize_text_field($stlcf_g_settings['recaptcha_site_key']) : '';
        $stlcf_v3_key    = isset( $stlcf_g_settings['recaptcha_v3_site_key'] ) ? sanitize_text_field($stlcf_g_settings['recaptcha_v3_site_key']) : '';

        $stlcf_radius = '0px';
        if ( $stlcf_b_design === 'style_rounded' ) { $stlcf_radius = '6px'; } 
        elseif ( $stlcf_b_design === 'style_pill' ) { $stlcf_radius = '50px'; }

        if ( $stlcf_c_type === 'recaptcha_v3' && ! empty( $stlcf_v3_key ) ) {
            $v3_inline = "
                jQuery(document).ready(function($) {
                    $('#stlcf-form-{$stlcf_form_id}').on('submit', function(e) {
                        var formScope = this;
                        if (!$(formScope).find('#stlcf-v3-token-{$stlcf_form_id}').val()) {
                            e.preventDefault();
                            grecaptcha.ready(function() {
                                grecaptcha.execute('" . esc_js( $stlcf_v3_key ) . "', {action: 'submit'}).then(function(token) {
                                    $('#stlcf-v3-token-{$stlcf_form_id}').val(token);
                                    formScope.submit();
                                });
                            });
                        }
                    });
                });
            ";
            wp_add_inline_script( 'stlcf-recaptcha-v3', $v3_inline );
        }

        $stlcf_is_offline = $this->is_currently_offline();
        $stlcf_offline_action = isset( $stlcf_g_settings['offline_action'] ) ? $stlcf_g_settings['offline_action'] : 'show_notice';
        $stlcf_offline_text = isset( $stlcf_g_settings['offline_message'] ) ? $stlcf_g_settings['offline_message'] : '';

        $stlcf_gdpr_en   = isset( $stlcf_g_settings['enable_gdpr'] ) ? $stlcf_g_settings['enable_gdpr'] : '0';
        $stlcf_gdpr_txt  = isset( $stlcf_g_settings['gdpr_text'] ) ? $stlcf_g_settings['gdpr_text'] : '';
        $stlcf_gdpr_page = isset( $stlcf_g_settings['gdpr_privacy_page'] ) ? $stlcf_g_settings['gdpr_privacy_page'] : '';

        ob_start();
        ?>
        <div class="stlcf-front-wrapper" style="max-width:400px; padding:20px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:<?php echo esc_attr( $stlcf_f_size ); ?>px; font-family:inherit; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div class="stlcf-status-box" style="display:none; padding:12px; border-radius:6px; margin-bottom:15px; font-size:0.9em; font-weight:500;"></div>

            <?php if ( $stlcf_is_offline && $stlcf_offline_action === 'show_notice' && ! empty( $stlcf_offline_text ) ) : ?>
                <div style="background:#fff3cd; color:#856404; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #ffeeba; font-size:0.9em; font-weight:500;">⚠️ <?php echo esc_html( $stlcf_offline_text ); ?></div>
            <?php endif; ?>
            
            <h3 style="margin-top:0; margin-bottom:15px; font-size: 1.2em; font-weight:600;"><?php echo esc_html( $stlcf_form_data->title ); ?></h3>
            
            <form id="stlcf-form-<?php echo esc_attr( $stlcf_form_id ); ?>" class="stlcf-ajax-action-form" method="POST" action="">
                <input type="hidden" class="stlcf-token-field" name="stlcf_frontend_token" value="<?php echo esc_attr( wp_create_nonce( 'stlcf_frontend_nonce_action' ) ); ?>">
                <input type="hidden" name="stlcf_submitted_form_id" value="<?php echo esc_attr( $stlcf_form_id ); ?>">
                <input type="hidden" class="stlcf-page-referer-url" name="stlcf_page_referer" value="">
                
                <?php if ( $stlcf_c_type === 'recaptcha_v3' ) : ?>
                    <input type="hidden" id="stlcf-v3-token-<?php echo esc_attr( $stlcf_form_id ); ?>" name="stlcf_recaptcha_v3_token">
                <?php endif; ?>

                <?php 
                if ( is_array( $stlcf_fields ) ) {
                    foreach ( $stlcf_fields as $stlcf_idx => $stlcf_field ) {
                        $stlcf_f_id = 'field_' . $stlcf_form_id . '_' . $stlcf_idx;
                        $stlcf_f_req = ( isset( $stlcf_field['required'] ) && $stlcf_field['required'] );
                        ?>
                        <div style="margin-bottom:12px;">
                            <label for="<?php echo esc_attr( $stlcf_f_id ); ?>" style="display:block; margin-bottom:5px; font-weight:500; font-size:0.9em;">
                                <?php echo esc_html( $stlcf_field['label'] ); ?>
                                <?php if ( $stlcf_f_req ) : ?>
                                    <span style="color:red;">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'textarea' ) : ?>
                                <textarea id="<?php echo esc_attr( $stlcf_f_id ); ?>" name="stlcf_input[<?php echo esc_attr( $stlcf_field['label'] ); ?>]" rows="4" style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; box-sizing:border-box;" <?php echo $stlcf_f_req ? 'required' : ''; ?>></textarea>
                            <?php elseif ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'agent_select' ) : ?>
                                <select id="<?php echo esc_attr( $stlcf_f_id ); ?>" name="stlcf_input[<?php echo esc_attr( $stlcf_field['label'] ); ?>]" style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; box-sizing:border-box; height:38px;" <?php echo $stlcf_f_req ? 'required' : ''; ?>>
                                    <option value="" selected disabled><?php esc_html_e( 'Choose Department / Agent...', 'sanirtech-lead-chat-forms' ); ?></option>
                                    <?php
                                    $stlcf_lines = isset( $stlcf_field['routing'] ) ? explode( "\n", $stlcf_field['routing'] ) : array();
                                    foreach ( $stlcf_lines as $stlcf_line ) {
                                        $stlcf_line = trim( $stlcf_line );
                                        if ( empty( $stlcf_line ) ) { continue; }
                                        $stlcf_parts = explode( '|', $stlcf_line );
                                        $stlcf_name  = isset( $stlcf_parts[0] ) ? trim( $stlcf_parts[0] ) : '';
                                        $stlcf_phone = isset( $stlcf_parts[1] ) ? preg_replace( '/[^0-9]/', '', $stlcf_parts[1] ) : '';
                                        if ( ! empty( $stlcf_name ) && ! empty( $stlcf_phone ) ) {
                                            echo '<option value="' . esc_attr( $stlcf_name . '|' . $stlcf_phone ) . '">' . esc_html( $stlcf_name ) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            <?php else : 
                                // ==========================================
                                // SMART PHONE DETECTION LOGIC
                                // ==========================================
                                $stlcf_lbl_lower = strtolower( $stlcf_field['label'] );
                                
                                // Check if the label contains phone-related keywords OR if the admin selected "Number" type
                                $stlcf_is_phone = ( strpos( $stlcf_lbl_lower, 'phone' ) !== false || strpos( $stlcf_lbl_lower, 'mobile' ) !== false || strpos( $stlcf_lbl_lower, 'whatsapp' ) !== false || ( isset($stlcf_field['type']) && $stlcf_field['type'] === 'number' ) );
                                
                                // Dynamically assign input type and our special JS tracking class
                                $stlcf_i_type  = $stlcf_is_phone ? 'tel' : ( ( isset( $stlcf_field['type'] ) && in_array( $stlcf_field['type'], array( 'text', 'email', 'number' ) ) ) ? $stlcf_field['type'] : 'text' );
                                $stlcf_p_class = $stlcf_is_phone ? 'stlcf-smart-phone' : '';
                                // ==========================================
                            ?>
                                <input type="<?php echo esc_attr( $stlcf_i_type ); ?>" class="<?php echo esc_attr( $stlcf_p_class ); ?>" id="<?php echo esc_attr( $stlcf_f_id ); ?>" name="stlcf_input[<?php echo esc_attr( $stlcf_field['label'] ); ?>]" style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; box-sizing:border-box; height:38px;" <?php echo $stlcf_f_req ? 'required' : ''; ?>>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                ?>
                
                <?php if ( $stlcf_c_type === 'built_in' ) : 
                    $stlcf_n1 = wp_rand(1, 9); $stlcf_n2 = wp_rand(1, 9); $stlcf_c_hash = wp_hash( $stlcf_n1 + $stlcf_n2 );
                    ?>
                    <div style="margin-bottom:12px;">
                        <label style="display:block; margin-bottom:5px; font-weight:500; font-size:0.9em;">
                            <?php echo esc_html( sprintf( 
                                /* translators: 1: First math number, 2: Second math number */
                                __( 'Spam Protection: What is %1$d + %2$d? *', 'sanirtech-lead-chat-forms' ), 
                                $stlcf_n1, $stlcf_n2 
                            ) ); ?>
                        </label>
                        <input type="hidden" name="stlcf_captcha_hash" value="<?php echo esc_attr( $stlcf_c_hash ); ?>">
                        <input type="number" name="stlcf_captcha_ans" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box; height:38px;" required>
                    </div>
                <?php elseif ( $stlcf_c_type === 'turnstile' && ! empty( $stlcf_ts_key ) ) : ?>
                    <div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $stlcf_ts_key ); ?>" style="margin-bottom:15px;"></div>
                <?php elseif ( $stlcf_c_type === 'recaptcha' && ! empty( $stlcf_rc_key ) ) : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $stlcf_rc_key ); ?>" style="margin-bottom:15px;"></div>
                <?php endif; ?>

                <?php if ( $stlcf_gdpr_en === '1' && ! empty( $stlcf_gdpr_txt ) ) : 
                    $stlcf_privacy_url = ! empty( $stlcf_gdpr_page ) ? get_permalink( $stlcf_gdpr_page ) : '#';
                    $stlcf_formatted_text = str_replace( '[privacy_link]', '<a href="' . esc_url( $stlcf_privacy_url ) . '" target="_blank" rel="noopener noreferrer">', $stlcf_gdpr_txt );
                    $stlcf_formatted_text = str_replace( '[/privacy_link]', '</a>', $stlcf_formatted_text );
                    ?>
                    <div class="stlcf-gdpr-consent-wrapper" style="margin-bottom:15px; display:flex; align-items:flex-start; gap:8px;">
                        <input type="checkbox" id="stlcf_gdpr_consent_<?php echo esc_attr( $stlcf_form_id ); ?>" name="stlcf_gdpr_consent" value="1" style="margin-top:3px;" required>
                        <label for="stlcf_gdpr_consent_<?php echo esc_attr( $stlcf_form_id ); ?>" style="font-size:0.85em; color:#475569; line-height:1.4; user-select:none;">
                            <?php echo wp_kses_post( $stlcf_formatted_text ); ?> <span style="color:red;">*</span>
                        </label>
                    </div>
                <?php endif; ?>

                <div class="stlcf-btn-flex-row" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:15px;">
                    <?php if ( ! ( $stlcf_is_offline && $stlcf_offline_action === 'email_only' ) ) : ?>
                        <button type="submit" class="stlcf-submit-trigger" name="stlcf_submit_type" value="whatsapp" style="flex:1; min-width:140px; background-color:<?php echo esc_attr( $stlcf_wa_clr ); ?>; color:#fff; padding:12px; border:none; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>; font-weight:bold; font-size:0.95em; cursor:pointer;">
                            <?php echo esc_html( $stlcf_wa_txt ); ?>
                        </button>
                    <?php endif; ?>
                    <?php if ( $stlcf_em_btn_en === '1' || ( $stlcf_is_offline && $stlcf_offline_action === 'email_only' ) ) : ?>
                        <button type="submit" class="stlcf-submit-trigger" name="stlcf_submit_type" value="email" style="flex:1; min-width:140px; background-color:<?php echo esc_attr( $stlcf_em_clr ); ?>; color:#fff; padding:12px; border:none; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>; font-weight:bold; font-size:0.95em; cursor:pointer;">
                            <?php echo esc_html( $stlcf_em_txt ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_nonce_refresh() {
        wp_send_json_success( array( 'token' => wp_create_nonce( 'stlcf_frontend_nonce_action' ) ) );
    }

    public function handle_ajax_form_submission() {
        if ( ! isset( $_POST['stlcf_frontend_token'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_frontend_token'] ) ), 'stlcf_frontend_nonce_action' ) ) { 
            wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please reload.', 'sanirtech-lead-chat-forms' ) ) );
        }
        if ( ! isset( $_POST['stlcf_submitted_form_id'] ) ) { wp_send_json_error( array( 'message' => esc_html__( 'Invalid form parameters.', 'sanirtech-lead-chat-forms' ) ) ); }
        $stlcf_form_id = intval( wp_unslash( $_POST['stlcf_submitted_form_id'] ) );
        if ( empty( $stlcf_form_id ) || ! isset( $_POST['stlcf_input'] ) || ! is_array( $_POST['stlcf_input'] ) ) { 
            wp_send_json_error( array( 'message' => esc_html__( 'Please fill out required fields.', 'sanirtech-lead-chat-forms' ) ) ); 
        }
        
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );

        $stlcf_gdpr_enabled = isset( $stlcf_g_settings['enable_gdpr'] ) ? $stlcf_g_settings['enable_gdpr'] : '0';
        if ( $stlcf_gdpr_enabled === '1' && ! isset( $_POST['stlcf_gdpr_consent'] ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'GDPR Compliance alert: You must accept our privacy policy statement checkboxes.', 'sanirtech-lead-chat-forms' ) ) );
        }

        $stlcf_c_type = isset( $stlcf_g_settings['captcha_type'] ) ? $stlcf_g_settings['captcha_type'] : 'none';
        if ( $stlcf_c_type === 'built_in' ) {
            $stlcf_u_ans = isset( $_POST['stlcf_captcha_ans'] ) ? intval( wp_unslash( $_POST['stlcf_captcha_ans'] ) ) : 0;
            $stlcf_exp_hash = isset( $_POST['stlcf_captcha_hash'] ) ? sanitize_text_field( wp_unslash( $_POST['stlcf_captcha_hash'] ) ) : '';
            if ( empty( $stlcf_exp_hash ) || wp_hash( $stlcf_u_ans ) !== $stlcf_exp_hash ) { 
                wp_send_json_error( array( 'message' => esc_html__( 'Incorrect spam protection answer.', 'sanirtech-lead-chat-forms' ) ) );
            }
        } elseif ( $stlcf_c_type === 'turnstile' ) {
            $stlcf_t_token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
            $stlcf_t_sec = isset( $stlcf_g_settings['turnstile_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['turnstile_secret_key'] ) : '';
            $stlcf_t_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            $stlcf_t_ver = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array( 'body' => array( 'secret' => $stlcf_t_sec, 'response' => $stlcf_t_token, 'remoteip' => $stlcf_t_ip ) ));
            $stlcf_t_body = json_decode( wp_remote_retrieve_body( $stlcf_t_ver ), true );
            if ( ! isset( $stlcf_t_body['success'] ) || ! $stlcf_t_body['success'] ) { 
                wp_send_json_error( array( 'message' => esc_html__( 'Turnstile verification failed.', 'sanirtech-lead-chat-forms' ) ) );
            }
        } elseif ( $stlcf_c_type === 'recaptcha' ) {
            $stlcf_r_token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
            $stlcf_r_sec = isset( $stlcf_g_settings['recaptcha_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['recaptcha_secret_key'] ) : '';
            $stlcf_r_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            $stlcf_r_ver = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array( 'body' => array( 'secret' => $stlcf_r_sec, 'response' => $stlcf_r_token, 'remoteip' => $stlcf_r_ip ) ));
            $stlcf_r_body = json_decode( wp_remote_retrieve_body( $stlcf_r_ver ), true );
            if ( ! isset( $stlcf_r_body['success'] ) || ! $stlcf_r_body['success'] ) { 
                wp_send_json_error( array( 'message' => esc_html__( 'reCAPTCHA verification failed.', 'sanirtech-lead-chat-forms' ) ) );
            }
        } elseif ( $stlcf_c_type === 'recaptcha_v3' ) {
            $stlcf_v3_tok = isset( $_POST['stlcf_recaptcha_v3_token'] ) ? sanitize_text_field( wp_unslash( $_POST['stlcf_recaptcha_v3_token'] ) ) : '';
            $stlcf_v3_sec = isset( $stlcf_g_settings['recaptcha_v3_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['recaptcha_v3_secret_key'] ) : '';
            $stlcf_v3_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            $stlcf_v3_ver = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array('body' => array( 'secret' => $stlcf_v3_sec, 'response' => $stlcf_v3_tok, 'remoteip' => $stlcf_v3_ip )));
            $stlcf_v3_body = json_decode( wp_remote_retrieve_body( $stlcf_v3_ver ), true );
            if ( ! isset( $stlcf_v3_body['success'] ) || ! $stlcf_v3_body['success'] || ( isset($stlcf_v3_body['score']) && $stlcf_v3_body['score'] < 0.5 ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'reCAPTCHA v3 spam mitigation triggered.', 'sanirtech-lead-chat-forms' ) ) );
            }
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Inputs are sanitized securely inside the subsequent foreach loop below.
        $stlcf_raw_inputs = wp_unslash( $_POST['stlcf_input'] );
        $stlcf_sanitized = array();
        $stlcf_dynamic_agent_phone = '';

        if ( is_array( $stlcf_raw_inputs ) ) {
            foreach ( $stlcf_raw_inputs as $stlcf_lbl => $stlcf_val ) {
                $stlcf_val_clean = sanitize_textarea_field( $stlcf_val );
                if ( strpos( $stlcf_val_clean, '|' ) !== false ) {
                    $stlcf_data_chunks = explode( '|', $stlcf_val_clean );
                    $stlcf_agent_name  = isset( $stlcf_data_chunks[0] ) ? trim( $stlcf_data_chunks[0] ) : '';
                    $stlcf_agent_phone = isset( $stlcf_data_chunks[1] ) ? preg_replace( '/[^0-9]/', '', $stlcf_data_chunks[1] ) : '';
                    if ( ! empty( $stlcf_agent_phone ) ) { $stlcf_dynamic_agent_phone = $stlcf_agent_phone; }
                    $stlcf_sanitized[ sanitize_text_field( $stlcf_lbl ) ] = $stlcf_agent_name;
                } else {
                    $stlcf_sanitized[ sanitize_text_field( $stlcf_lbl ) ] = $stlcf_val_clean;
                }
            }
        }
        
        $stlcf_pg_url = isset( $_POST['stlcf_page_referer'] ) ? esc_url_raw( wp_unslash( $_POST['stlcf_page_referer'] ) ) : home_url();
        $stlcf_tracking_enabled = isset( $stlcf_g_settings['enable_advanced_tracking'] ) ? $stlcf_g_settings['enable_advanced_tracking'] : '1';
        $stlcf_tracking_payload = array();

        if ( $stlcf_tracking_enabled === '1' ) {
            $stlcf_url_parsed = wp_parse_url( $stlcf_pg_url );
            if ( isset( $stlcf_url_parsed['query'] ) ) {
                parse_str( $stlcf_url_parsed['query'], $stlcf_query_vars );
                if ( ! empty( $stlcf_query_vars['utm_source'] ) ) { $stlcf_tracking_payload['UTM Source'] = sanitize_text_field( $stlcf_query_vars['utm_source'] ); }
                if ( ! empty( $stlcf_query_vars['utm_medium'] ) ) { $stlcf_tracking_payload['UTM Medium'] = sanitize_text_field( $stlcf_query_vars['utm_medium'] ); }
                if ( ! empty( $stlcf_query_vars['utm_campaign'] ) ) { $stlcf_tracking_payload['UTM Campaign'] = sanitize_text_field( $stlcf_query_vars['utm_campaign'] ); }
            }
            $stlcf_mapped_post_id = url_to_postid( $stlcf_pg_url );
            if ( $stlcf_mapped_post_id > 0 ) {
                $stlcf_tracking_payload['Page Context'] = get_the_title( $stlcf_mapped_post_id ) . ' (ID: ' . $stlcf_mapped_post_id . ')';
            }
        }

        // ======================================================================
        // 🚀 ENTERPRISE WEBHOOK DISPATCHER (ZAPIER / MAKE / PABBLY)
        // ======================================================================
        $stlcf_webhook_enabled = isset( $stlcf_g_settings['enable_webhook'] ) ? $stlcf_g_settings['enable_webhook'] : '0';
        $stlcf_webhook_url     = isset( $stlcf_g_settings['webhook_url'] ) ? esc_url_raw( $stlcf_g_settings['webhook_url'] ) : '';
        $stlcf_s_mod = isset( $_POST['stlcf_submit_channel'] ) ? sanitize_key( wp_unslash( $_POST['stlcf_submit_channel'] ) ) : 'whatsapp';

        if ( $stlcf_webhook_enabled === '1' && ! empty( $stlcf_webhook_url ) ) {
            
            // Build a clean, structured JSON payload for external CRMs
            $stlcf_webhook_payload = array(
                'event'        => 'new_lead_submission',
                'timestamp'    => current_time( 'mysql' ),
                'form_id'      => $stlcf_form_id,
                'source_url'   => $stlcf_pg_url,
                'channel'      => $stlcf_s_mod, // whatsapp or email
                'user_data'    => $stlcf_sanitized,
                'tracking'     => $stlcf_tracking_payload
            );

            // Dispatch HTTP request asynchronously (blocking => false) 
            // This guarantees the user doesn't wait for Zapier's server response
            wp_remote_post( $stlcf_webhook_url, array(
                'method'      => 'POST',
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => false, // Crucial for frontend performance!
                'headers'     => array(
                    'Content-Type' => 'application/json; charset=utf-8'
                ),
                'body'        => wp_json_encode( $stlcf_webhook_payload ),
                'cookies'     => array()
            ));
        }
        // ======================================================================

        global $wpdb;
        $stlcf_sv_db  = isset( $stlcf_g_settings['save_to_db'] ) ? $stlcf_g_settings['save_to_db'] : '1';
        if ( $stlcf_sv_db === '1' ) {
            $stlcf_t_ent = $wpdb->prefix . 'stlcf_entries';
            $stlcf_db_store = array_merge( $stlcf_sanitized, $stlcf_tracking_payload );
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->insert( $stlcf_t_ent, array( 'form_id' => $stlcf_form_id, 'form_data' => maybe_serialize( $stlcf_db_store ), 'page_url' => $stlcf_pg_url, 'submitted_at' => current_time( 'mysql' ) ), array( '%d', '%s', '%s', '%s' ) );
            // phpcs:enable
        }

        $stlcf_wa_txt = ""; $stlcf_em_txt = "";
        foreach ( $stlcf_sanitized as $stlcf_lbl => $stlcf_val ) { 
            $stlcf_wa_txt .= "*" . trim($stlcf_lbl) . ":* " . trim($stlcf_val) . "\n"; 
            $stlcf_em_txt .= trim($stlcf_lbl) . ": " . trim($stlcf_val) . "\r\n"; 
        }
        
        $stlcf_wa_tracking_str = ""; $stlcf_em_tracking_str = "";
        if ( ! empty( $stlcf_tracking_payload ) ) {
            $stlcf_wa_tracking_str .= "\n*Tracking Metadata*\n------------------------\n";
            foreach ( $stlcf_tracking_payload as $t_lbl => $t_val ) {
                $stlcf_wa_tracking_str .= "*" . trim($t_lbl) . ":* " . trim($t_val) . "\n";
                $stlcf_em_tracking_str .= trim($t_lbl) . ": " . trim($t_val) . "\r\n";
            }
        }

        $stlcf_offline_action = isset( $stlcf_g_settings['offline_action'] ) ? $stlcf_g_settings['offline_action'] : 'show_notice';

        if ( $this->is_currently_offline() && $stlcf_offline_action === 'email_only' ) {
            $stlcf_s_mod = 'email';
        }

        if ( $stlcf_s_mod === 'email' ) {
            $stlcf_rec_mb = isset( $stlcf_g_settings['admin_email_receiver'] ) && ! empty( $stlcf_g_settings['admin_email_receiver'] ) ? sanitize_email( $stlcf_g_settings['admin_email_receiver'] ) : get_option( 'admin_email' );
            $stlcf_em_sub = esc_html__( 'New Form Lead Submission', 'sanirtech-lead-chat-forms' );
            $stlcf_em_bdy  = "Hello Admin,\r\n\r\nA new lead has been generated from your website.\r\n----------------------------------------\r\n" . $stlcf_em_txt;
            if ( ! empty( $stlcf_em_tracking_str ) ) { $stlcf_em_bdy .= "----------------------------------------\r\n" . $stlcf_em_tracking_str; }
            $stlcf_em_bdy .= "----------------------------------------\r\nSubmitted from: " . $stlcf_pg_url . "\r\n";

            wp_mail( $stlcf_rec_mb, $stlcf_em_sub, $stlcf_em_bdy );

            $stlcf_ar_active = isset( $stlcf_g_settings['enable_auto_responder'] ) ? $stlcf_g_settings['enable_auto_responder'] : '0';
            if ( $stlcf_ar_active === '1' ) {
                $stlcf_client_target_email = '';
                $stlcf_client_extracted_name = 'Customer';

                foreach ( $stlcf_sanitized as $s_lbl => $s_val ) {
                    $s_lbl_clean = strtolower( trim( $s_lbl ) );
                    if ( is_email( $s_val ) ) { $stlcf_client_target_email = sanitize_email( $s_val ); }
                    if ( strpos( $s_lbl_clean, 'name' ) !== false || strpos( $s_lbl_clean, 'naam' ) !== false ) { $stlcf_client_extracted_name = sanitize_text_field( $s_val ); }
                }

                if ( ! empty( $stlcf_client_target_email ) ) {
                    $stlcf_ar_subject_blueprint = isset( $stlcf_g_settings['auto_responder_subject'] ) ? sanitize_text_field( $stlcf_g_settings['auto_responder_subject'] ) : 'Acknowledgement Notice';
                    $stlcf_ar_message_blueprint = isset( $stlcf_g_settings['auto_responder_message'] ) ? $this->rows_textarea_clean_escape( $stlcf_g_settings['auto_responder_message'] ) : '';
                    if ( empty( $stlcf_ar_message_blueprint ) ) { $stlcf_ar_message_blueprint = "Hi [Your Name],\r\n\r\nThank you for reaching out."; }

                    $stlcf_form_title_raw = isset( $stlcf_form_data->title ) && ! empty( $stlcf_form_data->title ) ? sanitize_text_field( $stlcf_form_data->title ) : 'Contact Desk Request';
                    $stlcf_ar_message_blueprint = str_replace( '[Your Name]', $stlcf_client_extracted_name, $stlcf_ar_message_blueprint );
                    $stlcf_ar_message_blueprint = str_replace( '[Form Title]', $stlcf_form_title_raw, $stlcf_ar_message_blueprint );

                    $stlcf_ar_headers = array( 'Content-Type: text/plain; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' );
                    wp_mail( $stlcf_client_target_email, $stlcf_ar_subject_blueprint, $stlcf_ar_message_blueprint, $stlcf_ar_headers );
                }
            }
            
            wp_send_json_success( array(
                'channel' => 'email',
                'message' => esc_html__( 'Thank you! Your lead information has been sent successfully via server email.', 'sanirtech-lead-chat-forms' )
            ) );
        } else {
            $stlcf_wa_msg  = "*New Lead Received*\n------------------------\n" . $stlcf_wa_txt . $stlcf_wa_tracking_str . "------------------------\n*Source:* " . $stlcf_pg_url;
            $stlcf_base_phone = isset( $stlcf_g_settings['global_phone'] ) ? preg_replace( '/[^0-9]/', '', $stlcf_g_settings['global_phone'] ) : '';
            $stlcf_final_target_routing = ! empty( $stlcf_dynamic_agent_phone ) ? $stlcf_dynamic_agent_phone : $stlcf_base_phone;
            $stlcf_tg_url = "https://wa.me/" . $stlcf_final_target_routing . "?text=" . rawurlencode( $stlcf_wa_msg );
            
            wp_send_json_success( array(
                'channel'      => 'whatsapp',
                'redirect_url' => esc_url_raw( $stlcf_tg_url )
            ) );
        }
    }

    private function compile_structured_schema_payload() {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_base_phone = isset( $stlcf_g_settings['global_phone'] ) ? preg_replace( '/[^0-9]/', '', $stlcf_g_settings['global_phone'] ) : '';
        return array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            '@id'      => esc_url( home_url( '/' ) ) . '#stlcf-organization',
            'name'     => esc_html( get_bloginfo( 'name' ) ),
            'url'      => esc_url( home_url( '/' ) ),
            'contactPoint' => array(
                '@type'       => 'ContactPoint',
                'telephone'   => '+' . $stlcf_base_phone,
                'contactType' => 'customer service',
                'url'         => 'https://wa.me/' . $stlcf_base_phone,
                'availableLanguage' => array( 'English' )
            )
        );
    }

    public function yoast_inject_schema_piece( $pieces, $context ) {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        if ( isset( $stlcf_g_settings['enable_seo_schema'] ) && $stlcf_g_settings['enable_seo_schema'] === '1' && $this->form_rendered ) {
            $pieces[] = $this->compile_structured_schema_payload();
        }
        return $pieces;
    }

    public function rank_math_inject_schema( $data, $jsonld ) {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        if ( isset( $stlcf_g_settings['enable_seo_schema'] ) && $stlcf_g_settings['enable_seo_schema'] === '1' && $this->form_rendered ) {
            $data['stlcf_whatsapp_routing'] = $this->compile_structured_schema_payload();
        }
        return $data;
    }

    public function inject_fallback_standalone_schema() {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        if ( ! isset( $stlcf_g_settings['enable_seo_schema'] ) || $stlcf_g_settings['enable_seo_schema'] !== '1' || ! $this->form_rendered ) { return; }
        if ( has_filter( 'wpseo_schema_graph_pieces' ) || has_filter( 'rank_math/json_ld' ) ) { return; }
        $schema_payload = $this->compile_structured_schema_payload();
        if ( is_array( $schema_payload ) ) {
            echo "\n\n";
            echo '<script type="application/ld+json">' . wp_json_encode( $schema_payload ) . "</script>\n";
        }
    }

    private function rows_textarea_clean_escape( $text_string ) {
        return implode( "\r\n", array_map( 'sanitize_text_field', explode( "\n", str_replace( "\r", '', $text_string ) ) ) );
    }

    private function is_currently_offline() {
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_hours_active = isset( $stlcf_g_settings['enable_business_hours'] ) ? $stlcf_g_settings['enable_business_hours'] : '0';
        if ( $stlcf_hours_active !== '1' ) { return false; }
        $stlcf_tz_target = isset( $stlcf_g_settings['business_timezone'] ) ? $stlcf_g_settings['business_timezone'] : 'UTC';
        try {
            $stlcf_tz_object = new DateTimeZone( $stlcf_tz_target );
            $stlcf_time_now  = new DateTime( 'now', $stlcf_tz_object );
        } catch ( Exception $e ) { return false; }
        $stlcf_day_slug = strtolower( $stlcf_time_now->format( 'l' ) ); 
        $stlcf_clock_check = $stlcf_time_now->format( 'H:i' );
        $stlcf_days_approved = isset( $stlcf_g_settings['business_days'] ) ? $stlcf_g_settings['business_days'] : array();
        if ( ! is_array( $stlcf_days_approved ) || ! isset( $stlcf_days_approved[$stlcf_day_slug] ) || $stlcf_days_approved[$stlcf_day_slug] !== '1' ) { return true; }
        $stlcf_start_frame = isset( $stlcf_g_settings['business_start'] ) ? $stlcf_g_settings['business_start'] : '09:00';
        $stlcf_end_frame   = isset( $stlcf_g_settings['business_end'] ) ? $stlcf_g_settings['business_end'] : '18:00';
        if ( $stlcf_clock_check < $stlcf_start_frame || $stlcf_clock_check > $stlcf_end_frame ) { return true; }
        return false;
    }
}