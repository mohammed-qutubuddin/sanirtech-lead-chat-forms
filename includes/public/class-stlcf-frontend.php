<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class STLCF_Frontend {

    public function __construct() {
        // FIXED: Replaced generic 'whatsapp_form' with prefixed 'stlcf_chat_form'
        add_shortcode( 'stlcf_chat_form', array( $this, 'render_dynamic_form' ) );
        add_action( 'template_redirect', array( $this, 'handle_frontend_form_submission' ) );
        add_action( 'wp_footer', array( $this, 'render_floating_widget' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
    }

    public function enqueue_frontend_scripts() {
        // Enqueue our frontend public stylesheet from its new path
        wp_enqueue_style(
            'stlcf-public-style',
            STLCF_PLUGIN_URL . 'assets/public/css/stlcf-public.css',
            array(),
            STLCF_VERSION
        );

        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
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

    public function render_floating_widget() {
        $stlcf_settings = get_option( 'stlcf_general_settings', array() );
        
        $stlcf_float_btn = isset( $stlcf_settings['floating_btn'] ) ? $stlcf_settings['floating_btn'] : '0';
        $stlcf_global_ph = isset( $stlcf_settings['global_phone'] ) ? preg_replace( '/[^0-9+]/', '', $stlcf_settings['global_phone'] ) : '';
        $stlcf_fl_clr = isset( $stlcf_settings['float_btn_color'] ) ? sanitize_hex_color( $stlcf_settings['float_btn_color'] ) : ( isset($stlcf_settings['btn_color']) ? sanitize_hex_color($stlcf_settings['btn_color']) : '#25D366' );

        if ( $stlcf_float_btn !== '1' || empty( $stlcf_global_ph ) ) { return; }

        $stlcf_wa_url = "https://wa.me/" . esc_attr( $stlcf_global_ph );
        ?>
        <a href="<?php echo esc_url( $stlcf_wa_url ); ?>" target="_blank" rel="noopener" class="stlcf-floating-btn" style="position:fixed; bottom:30px; right:30px; width:60px; height:60px; background-color:<?php echo esc_attr( $stlcf_fl_clr ); ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(0,0,0,0.25); z-index:999999; text-decoration:none; transition:transform 0.2s ease-in-out;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width:30px; height:30px; fill:#ffffff;">
                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
        </a>
        <?php
    }

    public function render_dynamic_form( $atts ) {
        // FIXED: Updated third parameter to match the new shortcode tag
        $stlcf_attrs = shortcode_atts( array( 'id' => '' ), $atts, 'stlcf_chat_form' );
        $stlcf_form_id = intval( $stlcf_attrs['id'] );
        if ( empty( $stlcf_form_id ) ) { return ''; }

        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
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

        // Enqueue V3 specific inline script dynamically if needed
        if ( $stlcf_c_type === 'recaptcha_v3' && ! empty( $stlcf_v3_key ) ) {
            $v3_inline = "
                jQuery(document).ready(function($) {
                    $('#stlcf-form-{$stlcf_form_id}').on('submit', function(e) {
                        var formScope = this;
                        if (!$(formScope).find('#stlcf-v3-token-{$stlcf_form_id}').val()) {
                            e.preventDefault();
                            grecaptcha.ready(function() {
                                grecaptcha.execute('" . esc_js($stlcf_v3_key) . "', {action: 'submit'}).then(function(token) {
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

        ob_start();
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['stlcf_status'] ) && sanitize_key( wp_unslash( $_GET['stlcf_status'] ) ) === 'mail_success' ) {
            echo '<div style="background:#d1fae5; color:#065f46; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #a7f3d0;">' . esc_html__( 'Thank you! Your submission details have been dispatched via Email.', 'sanirtech-lead-chat-forms' ) . '</div>';
        }
        ?>
        <div class="stlcf-front-wrapper" style="max-width:400px; padding:20px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; font-size:<?php echo esc_attr( $stlcf_f_size ); ?>px; font-family:inherit; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0; margin-bottom:15px; font-size: 1.2em; font-weight:600;"><?php echo esc_html( $stlcf_form_data->title ); ?></h3>
            <form id="stlcf-form-<?php echo esc_attr( $stlcf_form_id ); ?>" method="POST" action="">
                <?php wp_nonce_field( 'stlcf_frontend_nonce_action', 'stlcf_frontend_token' ); ?>
                <input type="hidden" name="stlcf_front_submit" value="1">
                <input type="hidden" name="stlcf_submitted_form_id" value="<?php echo esc_attr( $stlcf_form_id ); ?>">
                
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
                            <?php else : 
                                $stlcf_i_type = ( isset( $stlcf_field['type'] ) && in_array( $stlcf_field['type'], array( 'text', 'email', 'number' ) ) ) ? $stlcf_field['type'] : 'text';
                                ?>
                                <input type="<?php echo esc_attr( $stlcf_i_type ); ?>" id="<?php echo esc_attr( $stlcf_f_id ); ?>" name="stlcf_input[<?php echo esc_attr( $stlcf_field['label'] ); ?>]" style="width:100%; padding:8px; border:1px solid #cbd5e0; border-radius:4px; box-sizing:border-box; height:38px;" <?php echo $stlcf_f_req ? 'required' : ''; ?>>
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
                            <?php 
                            /* translators: %1$d: first dynamic random number, %2$d: second dynamic random number */
                            echo esc_html( sprintf( __( 'Spam Protection: What is %1$d + %2$d? *', 'sanirtech-lead-chat-forms' ), $stlcf_n1, $stlcf_n2 ) ); 
                            ?>
                        </label>
                        <input type="hidden" name="stlcf_captcha_hash" value="<?php echo esc_attr( $stlcf_c_hash ); ?>">
                        <input type="number" name="stlcf_captcha_ans" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box; height:38px;" required>
                    </div>
                <?php elseif ( $stlcf_c_type === 'turnstile' && ! empty( $stlcf_ts_key ) ) : ?>
                    <div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $stlcf_ts_key ); ?>" style="margin-bottom:15px;"></div>
                <?php elseif ( $stlcf_c_type === 'recaptcha' && ! empty( $stlcf_rc_key ) ) : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $stlcf_rc_key ); ?>" style="margin-bottom:15px;"></div>
                <?php endif; ?>

                <div class="stlcf-btn-flex-row" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:15px;">
                    <button type="submit" name="stlcf_submit_type" value="whatsapp" style="flex:1; min-width:140px; background-color:<?php echo esc_attr( $stlcf_wa_clr ); ?>; color:#fff; padding:12px; border:none; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>; font-weight:bold; font-size:0.95em; cursor:pointer;">
                        <?php echo esc_html( $stlcf_wa_txt ); ?>
                    </button>
                    <?php if ( $stlcf_em_btn_en === '1' ) : ?>
                        <button type="submit" name="stlcf_submit_type" value="email" style="flex:1; min-width:140px; background-color:<?php echo esc_attr( $stlcf_em_clr ); ?>; color:#fff; padding:12px; border:none; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>; font-weight:bold; font-size:0.95em; cursor:pointer;">
                            <?php echo esc_html( $stlcf_em_txt ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_frontend_form_submission() {
        if ( isset( $_POST['stlcf_front_submit'] ) && sanitize_key( wp_unslash( $_POST['stlcf_front_submit'] ) ) === '1' ) {
            if ( ! isset( $_POST['stlcf_frontend_token'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_frontend_token'] ) ), 'stlcf_frontend_nonce_action' ) ) { 
                wp_die( esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) ); 
            }
            
            if ( ! isset( $_POST['stlcf_submitted_form_id'] ) ) { return; }
            $stlcf_form_id = intval( wp_unslash( $_POST['stlcf_submitted_form_id'] ) );
            if ( empty( $stlcf_form_id ) || ! isset( $_POST['stlcf_input'] ) || ! is_array( $_POST['stlcf_input'] ) ) { return; }
            
            $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
            $stlcf_c_type    = isset( $stlcf_g_settings['captcha_type'] ) ? $stlcf_g_settings['captcha_type'] : 'none';

            if ( $stlcf_c_type === 'built_in' ) {
                $stlcf_u_ans = isset( $_POST['stlcf_captcha_ans'] ) ? intval( wp_unslash( $_POST['stlcf_captcha_ans'] ) ) : 0;
                $stlcf_exp_hash = isset( $_POST['stlcf_captcha_hash'] ) ? sanitize_text_field( wp_unslash( $_POST['stlcf_captcha_hash'] ) ) : '';
                if ( empty( $stlcf_exp_hash ) || wp_hash( $stlcf_u_ans ) !== $stlcf_exp_hash ) { 
                    wp_die( esc_html__( 'Spam protection failed! Incorrect math calculation.', 'sanirtech-lead-chat-forms' ) ); 
                }
            } elseif ( $stlcf_c_type === 'turnstile' ) {
                $stlcf_t_token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
                $stlcf_t_sec = isset( $stlcf_g_settings['turnstile_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['turnstile_secret_key'] ) : '';
                $stlcf_t_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
                $stlcf_t_ver = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array( 'body' => array( 'secret' => $stlcf_t_sec, 'response' => $stlcf_t_token, 'remoteip' => $stlcf_t_ip ) ));
                $stlcf_t_body = json_decode( wp_remote_retrieve_body( $stlcf_t_ver ), true );
                if ( ! isset( $stlcf_t_body['success'] ) || ! $stlcf_t_body['success'] ) { 
                    wp_die( esc_html__( 'Cloudflare Turnstile validation failed.', 'sanirtech-lead-chat-forms' ) ); 
                }
            } elseif ( $stlcf_c_type === 'recaptcha' ) {
                $stlcf_r_token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
                $stlcf_r_sec = isset( $stlcf_g_settings['recaptcha_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['recaptcha_secret_key'] ) : '';
                $stlcf_r_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
                $stlcf_r_ver = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array( 'body' => array( 'secret' => $stlcf_r_sec, 'response' => $stlcf_r_token, 'remoteip' => $stlcf_r_ip ) ));
                $stlcf_r_body = json_decode( wp_remote_retrieve_body( $stlcf_r_ver ), true );
                if ( ! isset( $stlcf_r_body['success'] ) || ! $stlcf_r_body['success'] ) { 
                    wp_die( esc_html__( 'Google reCAPTCHA v2 verification failed.', 'sanirtech-lead-chat-forms' ) ); 
                }
            } elseif ( $stlcf_c_type === 'recaptcha_v3' ) {
                $stlcf_v3_tok = isset( $_POST['stlcf_recaptcha_v3_token'] ) ? sanitize_text_field( wp_unslash( $_POST['stlcf_recaptcha_v3_token'] ) ) : '';
                $stlcf_v3_sec = isset( $stlcf_g_settings['recaptcha_v3_secret_key'] ) ? sanitize_text_field( $stlcf_g_settings['recaptcha_v3_secret_key'] ) : '';
                $stlcf_v3_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
                
                $stlcf_v3_ver = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
                    'body' => array( 'secret' => $stlcf_v3_sec, 'response' => $stlcf_v3_tok, 'remoteip' => $stlcf_v3_ip )
                ));
                $stlcf_v3_body = json_decode( wp_remote_retrieve_body( $stlcf_v3_ver ), true );
                
                if ( ! isset( $stlcf_v3_body['success'] ) || ! $stlcf_v3_body['success'] || ( isset($stlcf_v3_body['score']) && $stlcf_v3_body['score'] < 0.5 ) ) {
                    wp_die( esc_html__( 'Google reCAPTCHA v3 failed. Spam bot behavior detected.', 'sanirtech-lead-chat-forms' ) );
                }
            }

            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $stlcf_raw_inputs = wp_unslash( $_POST['stlcf_input'] );
            $stlcf_sanitized = array();
            if ( is_array( $stlcf_raw_inputs ) ) {
                foreach ( $stlcf_raw_inputs as $stlcf_lbl => $stlcf_val ) {
                    $stlcf_sanitized[sanitize_text_field( $stlcf_lbl )] = sanitize_textarea_field( $stlcf_val );
                }
            }
            
            $stlcf_sv_db     = isset( $stlcf_g_settings['save_to_db'] ) ? $stlcf_g_settings['save_to_db'] : '1';
            $stlcf_gl_ph     = isset( $stlcf_g_settings['global_phone'] ) ? preg_replace( '/[^0-9+]/', '', $stlcf_g_settings['global_phone'] ) : '';
            $stlcf_pg_url    = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : home_url();
            
            global $wpdb;
            if ( $stlcf_sv_db === '1' ) {
                $stlcf_t_ent = $wpdb->prefix . 'stlcf_entries';
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->insert( $stlcf_t_ent, array( 'form_id' => $stlcf_form_id, 'form_data' => maybe_serialize( $stlcf_sanitized ), 'page_url' => $stlcf_pg_url, 'submitted_at' => current_time( 'mysql' ) ), array( '%d', '%s', '%s', '%s' ) );
                // phpcs:enable
            }

            // Formatting for Email and WhatsApp separately
            $stlcf_wa_txt = "";
            $stlcf_em_txt = "";

            foreach ( $stlcf_sanitized as $stlcf_lbl => $stlcf_val ) { 
                // WhatsApp Format: strictly using \n for line breaks
                $stlcf_wa_txt .= "*" . trim($stlcf_lbl) . ":* " . trim($stlcf_val) . "\n"; 
                // Email Format: \r\n is standard for emails
                $stlcf_em_txt .= trim($stlcf_lbl) . ": " . trim($stlcf_val) . "\r\n"; 
            }
            
            $stlcf_s_mod = isset( $_POST['stlcf_submit_type'] ) ? sanitize_key( wp_unslash( $_POST['stlcf_submit_type'] ) ) : 'whatsapp';

            if ( $stlcf_s_mod === 'email' ) {
                $stlcf_rec_mb = isset( $stlcf_g_settings['admin_email_receiver'] ) && ! empty( $stlcf_g_settings['admin_email_receiver'] ) ? sanitize_email( $stlcf_g_settings['admin_email_receiver'] ) : get_option( 'admin_email' );
                $stlcf_em_sub = esc_html__( 'New Form Lead Submission', 'sanirtech-lead-chat-forms' );
                
                // Constructing clean plain-text email with proper carriage returns
                $stlcf_em_bdy  = "Hello Admin,\r\n\r\n";
                $stlcf_em_bdy .= "A new lead has been generated from your website.\r\n";
                $stlcf_em_bdy .= "----------------------------------------\r\n";
                $stlcf_em_bdy .= $stlcf_em_txt;
                $stlcf_em_bdy .= "----------------------------------------\r\n";
                $stlcf_em_bdy .= "Submitted from: " . $stlcf_pg_url . "\r\n";

                wp_mail( $stlcf_rec_mb, $stlcf_em_sub, $stlcf_em_bdy );
                wp_safe_redirect( add_query_arg( 'stlcf_status', 'mail_success', $stlcf_pg_url ) );
                exit;
            } else {
                // WhatsApp Format: Clean and Standard
                $stlcf_wa_msg  = "*New Lead Received*\n";
                $stlcf_wa_msg .= "------------------------\n";
                $stlcf_wa_msg .= $stlcf_wa_txt;
                $stlcf_wa_msg .= "------------------------\n";
                $stlcf_wa_msg .= "*Source:* " . $stlcf_pg_url;
                
                // Final bulletproof link - using rawurlencode is the key
                $stlcf_tg_url = "https://wa.me/" . $stlcf_gl_ph . "?text=" . rawurlencode( $stlcf_wa_msg );
                
                // Force redirect
                wp_redirect( esc_url_raw( $stlcf_tg_url ) );
                exit;
            }
        }
    }
}