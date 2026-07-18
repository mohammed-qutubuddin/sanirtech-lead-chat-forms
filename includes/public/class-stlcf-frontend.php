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
        add_action( 'wp_ajax_stlcf_log_abandoned_lead', array( $this, 'handle_abandoned_lead_logging' ) );
        add_action( 'wp_ajax_nopriv_stlcf_log_abandoned_lead', array( $this, 'handle_abandoned_lead_logging' ) );

        add_action( 'wp_head', array( $this, 'inject_baseline_pixels_code' ), 5 );

        add_filter( 'wpseo_schema_graph_pieces', array( $this, 'yoast_inject_schema_piece' ), 11, 2 );
        add_filter( 'rank_math/json_ld', array( $this, 'rank_math_inject_schema' ), 11, 2 );
        add_action( 'wp_footer', array( $this, 'inject_fallback_standalone_schema' ), 99 );
        
        add_action( 'init', array( $this, 'register_gutenberg_block' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

        // WooCommerce integrations hooks
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'render_woo_product_whatsapp_button' ) );
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
            'fw_time_delay'    => isset( $stlcf_g_settings['fw_time_delay'] ) ? intval( $stlcf_g_settings['fw_time_delay'] ) : 0,
            'submitting_text'  => __( 'Processing...', 'sanirtech-lead-chat-forms' )
        ) );

        // Enqueue Smart Country Code Library if enabled
        $stlcf_geo_enabled = isset( $stlcf_g_settings['enable_geo_phone'] ) ? $stlcf_g_settings['enable_geo_phone'] : '1';
        if ( $stlcf_geo_enabled === '1' ) {
            wp_enqueue_style( 'intl-tel-input', STLCF_PLUGIN_URL . 'assets/public/css/intlTelInput.css', array(), '17.0.8' );
            wp_enqueue_script( 'intl-tel-input', STLCF_PLUGIN_URL . 'assets/public/js/intlTelInput.min.js', array( 'jquery' ), '17.0.8', true );
            
            // Pass the utils script URL to our local JS object for formatting
            wp_localize_script( 'stlcf-public-script', 'stlcf_iti_config', array(
                'utils_url' => STLCF_PLUGIN_URL . 'assets/public/js/utils.js'
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

        $stlcf_multi_agent = isset( $stlcf_settings['enable_multi_agent'] ) ? $stlcf_settings['enable_multi_agent'] : '0';
        $stlcf_agents = isset( $stlcf_settings['multi_agents_list'] ) ? maybe_unserialize( $stlcf_settings['multi_agents_list'] ) : array();
        if ( ! is_array( $stlcf_agents ) ) { $stlcf_agents = array(); }
        
        $visitor_country = $this->get_visitor_country();
        $filtered_agents = array();
        foreach ( $stlcf_agents as $agent ) {
            $allowed = isset( $agent['allowed_countries'] ) ? trim( $agent['allowed_countries'] ) : '';
            if ( empty( $allowed ) ) {
                $filtered_agents[] = $agent;
            } else {
                $allowed_list = array_map( 'trim', explode( ',', strtoupper( $allowed ) ) );
                if ( in_array( $visitor_country, $allowed_list, true ) ) {
                    $filtered_agents[] = $agent;
                }
            }
        }
        $stlcf_agents = $filtered_agents;
        
        $wobble_en = isset( $stlcf_settings['fw_wobble_animation'] ) ? $stlcf_settings['fw_wobble_animation'] : '0';
        $badge_en  = isset( $stlcf_settings['fw_notif_badge'] ) ? $stlcf_settings['fw_notif_badge'] : '0';
        
        $faq_list  = isset( $stlcf_settings['widget_faq_list'] ) ? maybe_unserialize( $stlcf_settings['widget_faq_list'] ) : array();
        if ( ! is_array( $faq_list ) ) { $faq_list = array(); }
        
        $offline_action  = isset( $stlcf_settings['offline_action'] ) ? $stlcf_settings['offline_action'] : 'show_notice';
        $offline_form_id = isset( $stlcf_settings['offline_form_id'] ) ? intval( $stlcf_settings['offline_form_id'] ) : 0;

        $stlcf_wa_url = "https://wa.me/" . esc_attr( $stlcf_global_ph );
        if ( ! empty( $stlcf_fw_msg ) ) {
            $stlcf_wa_url .= "?text=" . rawurlencode( $stlcf_fw_msg );
        }

        $stlcf_geom_pos = ( $stlcf_fw_pos === 'left' ) ? 'left:30px;' : 'right:30px;';
        $stlcf_tooltip_geom = ( $stlcf_fw_pos === 'left' ) ? 'left: 70px;' : 'right: 70px;';
        $flex_align = ( $stlcf_fw_pos === 'left' ) ? 'align-items: flex-start;' : 'align-items: flex-end;';
        ?>
        <div class="stlcf-floating-container stlcf-floating-widget" style="position:fixed; bottom:30px; <?php echo esc_attr( $stlcf_geom_pos ); ?> z-index:999999; display:flex; flex-direction:column; <?php echo esc_attr( $flex_align ); ?> gap: 15px;">
            
            <?php if ( ( $stlcf_multi_agent === '1' && ! empty( $stlcf_agents ) ) || ( $stlcf_is_offline && $offline_action === 'show_form' && $offline_form_id > 0 ) ) : ?>
                <div class="stlcf-multi-agent-panel" style="display:none; width:320px; background:#ffffff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); border:1px solid #e2e8f0; overflow:hidden; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin-bottom: 5px;">
                    <div class="stlcf-agent-panel-header" style="background:<?php echo esc_attr( $stlcf_fl_clr ); ?>; padding:16px; color:#ffffff; position:relative;">
                        <h4 style="margin:0; font-size:16px; font-weight:600; color:#ffffff; line-height:1.2;">
                            <?php 
                            if ( $stlcf_is_offline && $offline_action === 'show_form' ) {
                                esc_html_e( 'Leave a Message', 'sanirtech-lead-chat-forms' );
                            } else {
                                echo esc_html( ! empty( $stlcf_fw_txt ) ? $stlcf_fw_txt : __( 'How can we help you?', 'sanirtech-lead-chat-forms' ) );
                            }
                            ?>
                        </h4>
                        <p style="margin:4px 0 0 0; font-size:12px; opacity:0.9; color:#ffffff;">
                            <?php 
                            if ( $stlcf_is_offline && $offline_action === 'show_form' ) {
                                esc_html_e( 'We are offline. Send us your inquiry!', 'sanirtech-lead-chat-forms' );
                            } else {
                                esc_html_e( 'Click on an agent below to start chatting:', 'sanirtech-lead-chat-forms' );
                            }
                            ?>
                        </p>
                        <button type="button" class="stlcf-close-agent-panel-btn" style="position:absolute; top:12px; right:12px; background:transparent; border:none; color:#ffffff; font-size:20px; font-weight:bold; cursor:pointer; line-height:1; padding:0;">&times;</button>
                    </div>
                    <div class="stlcf-agent-panel-body" style="padding:12px; max-height:350px; overflow-y:auto; background:#ffffff;">
                        <?php if ( $stlcf_is_offline && $offline_action === 'show_form' ) : ?>
                            <div class="stlcf-offline-form-popup-embed" style="padding:5px;">
                                <?php echo do_shortcode( '[stlcf_chat_form id="' . intval( $offline_form_id ) . '"]' ); ?>
                            </div>
                        <?php else : ?>
                            <?php foreach ( $stlcf_agents as $agent ) : 
                                $a_name = isset( $agent['name'] ) ? $agent['name'] : '';
                                $a_title = isset( $agent['title'] ) ? $agent['title'] : '';
                            $a_phone = isset( $agent['phone'] ) ? preg_replace( '/[^0-9]/', '', $agent['phone'] ) : '';
                            $a_status = isset( $agent['status'] ) ? $agent['status'] : 'online';
                            $a_avatar = isset( $agent['avatar'] ) ? $agent['avatar'] : '';
                            
                            if ( empty( $a_avatar ) ) {
                                $a_avatar = 'https://secure.gravatar.com/avatar/' . md5( strtolower( trim( $a_name ) ) ) . '?s=80&d=mp';
                            }
                            
                            $a_wa_url = "https://wa.me/" . esc_attr( $a_phone );
                            if ( ! empty( $stlcf_fw_msg ) ) {
                                $a_wa_url .= "?text=" . rawurlencode( $stlcf_fw_msg );
                            }
                            
                            $status_color = '#22c55e';
                            if ( $a_status === 'away' ) { $status_color = '#eab308'; }
                            elseif ( $a_status === 'offline' ) { $status_color = '#94a3b8'; }
                        ?>
                            <a href="<?php echo esc_url( $a_wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="stlcf-agent-item" style="display:flex; align-items:center; gap:12px; padding:10px; border-radius:8px; text-decoration:none; margin-bottom:8px; transition:background 0.2s ease; background:#f8fafc; border:1px solid #f1f5f9;">
                                <div style="position:relative; width:40px; height:40px; flex-shrink:0;">
                                    <img src="<?php echo esc_url( $a_avatar ); ?>" alt="<?php echo esc_attr( $a_name ); ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover; display:block;">
                                    <span style="position:absolute; bottom:0; right:0; width:10px; height:10px; background:<?php echo esc_attr( $status_color ); ?>; border:2px solid #ffffff; border-radius:50%; display:block;"></span>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:600; font-size:13px; color:#0f172a; text-align:left; line-height:1.3;"><?php echo esc_html( $a_name ); ?></div>
                                    <div style="font-size:11px; color:#64748b; text-align:left; line-height:1.2;"><?php echo esc_html( $a_title ); ?></div>
                                </div>
                                <div style="font-size:9px; font-weight:700; color:<?php echo esc_attr( $status_color ); ?>; text-transform:uppercase; border:1px solid <?php echo esc_attr( $status_color ); ?>; padding:2px 6px; border-radius:20px; line-height:1;"><?php echo esc_html( $a_status ); ?></div>
                            </a>
                        <?php endforeach; ?>
                        
                        <?php if ( ! empty( $faq_list ) ) : ?>
                            <div class="stlcf-faq-accordion-wrapper" style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                                <h5 style="margin: 0 0 10px 0; font-size: 12px; color: #475569; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: 0.05em;"><?php esc_html_e( 'FAQ Accordion', 'sanirtech-lead-chat-forms' ); ?></h5>
                                <?php foreach ( $faq_list as $faq ) : ?>
                                    <div class="stlcf-faq-item" style="margin-bottom: 8px; border-bottom: 1px solid #f8fafc; padding-bottom: 6px;">
                                        <div class="stlcf-faq-question" style="font-size: 12px; font-weight: 600; color: #1e293b; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none;" onclick="var a = this.nextElementSibling; var s = this.querySelector('.stlcf-faq-icon'); if(a.style.display==='none'){ a.style.display='block'; s.textContent='−'; }else{ a.style.display='none'; s.textContent='+'; }">
                                            <span><?php echo esc_html( $faq['question'] ); ?></span>
                                            <span class="stlcf-faq-icon" style="font-weight: bold; font-size: 14px; margin-left: 5px; color: <?php echo esc_attr( $stlcf_fl_clr ); ?>;">+</span>
                                        </div>
                                        <div class="stlcf-faq-answer" style="display: none; font-size: 11px; color: #64748b; margin-top: 4px; line-height: 1.4; text-align: left;">
                                            <?php echo esc_html( $faq['answer'] ); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php 
                    $stlcf_powered_by = isset( $stlcf_settings['enable_powered_by'] ) ? $stlcf_settings['enable_powered_by'] : '1';
                    if ( $stlcf_powered_by === '1' ) : 
                    ?>
                        <div class="stlcf-powered-by-link" style="text-align:center; padding:8px 12px; background:#f8fafc; border-top:1px solid #f1f5f9; font-size:10px; color:#94a3b8; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            Powered by <a href="https://wordpress.org/plugins/sanirtech-lead-chat-forms/" target="_blank" rel="noopener" style="color:#64748b; text-decoration:none; font-weight:600;">SanirTech Lead Chat</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php
            $bubble_href = esc_url( $stlcf_wa_url );
            $bubble_target = 'target="_blank" rel="noopener noreferrer"';
            $bubble_class = 'stlcf-floating-bubble-link';
            if ( ( $stlcf_multi_agent === '1' && ! empty( $stlcf_agents ) ) || ( $stlcf_is_offline && $offline_action === 'show_form' && $offline_form_id > 0 ) ) {
                $bubble_href = '#';
                $bubble_target = '';
                $bubble_class .= ' stlcf-multi-agent-trigger';
            }
            if ( $wobble_en === '1' ) {
                $bubble_class .= ' stlcf-wobble-btn';
            }
            ?>

            <?php if ( ! empty( $stlcf_fw_txt ) && $stlcf_multi_agent !== '1' ) : ?>
                <div class="stlcf-fw-tooltip" style="position:absolute; bottom:15px; <?php echo esc_attr( $stlcf_tooltip_geom ); ?> background:#1e293b; color:#fff; padding:6px 12px; border-radius:4px; font-size:12px; font-weight:500; white-space:nowrap; box-shadow:0 2px 8px rgba(0,0,0,0.15); pointer-events:none; font-family: inherit;">
                    <?php echo esc_html( $stlcf_fw_txt ); ?>
                </div>
            <?php endif; ?>

            <a href="<?php echo esc_url( $bubble_href ); ?>" <?php echo ( $bubble_target === '' ) ? '' : 'target="_blank" rel="noopener noreferrer"'; ?> class="<?php echo esc_attr( $bubble_class ); ?>" style="position:relative; width:60px; height:60px; background-color:<?php echo esc_attr( $stlcf_fl_clr ); ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(0,0,0,0.25); text-decoration:none; transition:transform 0.2s ease-in-out;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width:30px; height:30px; fill:#ffffff;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                <?php if ( $badge_en === '1' ) : ?>
                    <span class="stlcf-notif-badge" style="position:absolute; top:-2px; right:-2px; width:14px; height:14px; background:#ef4444; border:2px solid #ffffff; border-radius:50%; box-shadow:0 2px 4px rgba(0,0,0,0.2); display:block; z-index:10;"></span>
                <?php endif; ?>
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
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $variants = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE ab_parent_id = %d AND status = 'active'", $stlcf_form_id ) );
        if ( ! empty( $variants ) ) {
            $parent_form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_form_id ) );
            if ( $parent_form && $parent_form->status === 'active' ) {
                $pool = array_merge( array( $parent_form ), $variants );
            } else {
                $pool = $variants;
            }
            $stlcf_form_data = $pool[ array_rand( $pool ) ];
            $stlcf_form_id   = intval( $stlcf_form_data->id );
        } else {
            $stlcf_form_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_form_id ) );
        }
        
        if ( $stlcf_form_data ) {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}stlcf_forms SET views = views + 1 WHERE id = %d", $stlcf_form_id ) );
        }
        // phpcs:enable

        if ( ! $stlcf_form_data ) { return ''; }
        if ( isset( $stlcf_form_data->status ) && $stlcf_form_data->status === 'inactive' ) {
            return '<div class="stlcf-inactive-form-notice" style="padding:15px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#64748b; font-family:-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif;">' . esc_html__( 'This contact form is currently inactive.', 'sanirtech-lead-chat-forms' ) . '</div>';
        }

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
        
        $f_brand_color = ! empty( $stlcf_form_data->brand_color ) ? sanitize_hex_color( $stlcf_form_data->brand_color ) : '';
        $f_btn_txt_clr = ! empty( $stlcf_form_data->button_text_color ) ? sanitize_hex_color( $stlcf_form_data->button_text_color ) : '';
        $f_radius      = ! empty( $stlcf_form_data->border_radius ) ? intval( $stlcf_form_data->border_radius ) : '';

        if ( ! empty( $f_brand_color ) || ! empty( $f_btn_txt_clr ) || $f_radius !== '' ) {
            echo '<style>';
            $selector = '#stlcf-form-wrapper-' . $stlcf_form_id;
            if ( ! empty( $f_brand_color ) ) {
                echo esc_html( "{$selector} .stlcf-submit-trigger { background-color: {$f_brand_color} !important; border-color: {$f_brand_color} !important; }" );
                echo esc_html( "{$selector} input:focus, {$selector} textarea:focus, {$selector} select:focus { border-color: {$f_brand_color} !important; box-shadow: 0 0 0 1px {$f_brand_color} !important; }" );
                echo esc_html( "{$selector} .stlcf-step-dot.stlcf-step-active { background-color: {$f_brand_color} !important; }" );
                echo esc_html( "{$selector} .stlcf-conversational-nav button { background-color: {$f_brand_color} !important; border-color: {$f_brand_color} !important; }" );
            }
            if ( ! empty( $f_btn_txt_clr ) ) {
                echo esc_html( "{$selector} .stlcf-submit-trigger { color: {$f_btn_txt_clr} !important; }" );
                echo esc_html( "{$selector} .stlcf-submit-trigger svg { fill: {$f_btn_txt_clr} !important; }" );
                echo esc_html( "{$selector} .stlcf-conversational-nav button { color: {$f_btn_txt_clr} !important; }" );
            }
            if ( $f_radius !== '' ) {
                echo esc_html( "{$selector} { border-radius: {$f_radius}px !important; }" );
                echo esc_html( "{$selector} input, {$selector} textarea, {$selector} select, {$selector} .stlcf-submit-trigger { border-radius: {$f_radius}px !important; }" );
            }
            echo '</style>';
        }
        ?>
        <div id="stlcf-form-wrapper-<?php echo intval( $stlcf_form_id ); ?>" class="stlcf-front-wrapper" style="font-size:<?php echo esc_attr( $stlcf_f_size ); ?>px;">
            <div class="stlcf-status-box"></div>

            <?php if ( $stlcf_is_offline && $stlcf_offline_action === 'show_notice' && ! empty( $stlcf_offline_text ) ) : ?>
                <div style="background:#fff3cd; color:#856404; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #ffeeba; font-size:0.9em; font-weight:500;">⚠️ <?php echo esc_html( $stlcf_offline_text ); ?></div>
            <?php endif; ?>
            
            <h3><?php echo esc_html( $this->translate_single_string( 'Form Title - ' . $stlcf_form_id, $stlcf_form_data->title ) ); ?></h3>
            
            <?php
            $has_file_field = false;
            if ( is_array( $stlcf_fields ) ) {
                foreach ( $stlcf_fields as $f ) {
                    if ( isset( $f['type'] ) && $f['type'] === 'file' ) {
                        $has_file_field = true;
                        break;
                    }
                }
            }
            ?>
            <form id="stlcf-form-<?php echo esc_attr( $stlcf_form_id ); ?>" class="stlcf-ajax-action-form" data-layout="<?php echo esc_attr( isset( $stlcf_form_data->layout ) ? $stlcf_form_data->layout : 'standard' ); ?>" method="POST" action="" <?php echo $has_file_field ? 'enctype="multipart/form-data"' : ''; ?>>
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
                        
                        $cond_enabled  = isset( $stlcf_field['cond_enabled'] ) ? intval( $stlcf_field['cond_enabled'] ) : 0;
                        $cond_field    = isset( $stlcf_field['cond_field'] ) ? $stlcf_field['cond_field'] : '';
                        $cond_operator = isset( $stlcf_field['cond_operator'] ) ? $stlcf_field['cond_operator'] : 'equals';
                        $cond_value    = isset( $stlcf_field['cond_value'] ) ? $stlcf_field['cond_value'] : '';
                        
                        $cond_attrs = '';
                        $group_class = 'stlcf-field-group';
                        $group_style = '';
                        if ( $cond_enabled ) {
                            $group_class .= ' stlcf-cond-logic-field';
                            $cond_attrs = ' data-cond-field="' . esc_attr( $cond_field ) . '" data-cond-operator="' . esc_attr( $cond_operator ) . '" data-cond-value="' . esc_attr( $cond_value ) . '"';
                            $group_style = 'display:none;';
                        }
                        
                        $field_label = $this->translate_single_string( 'Form ' . $stlcf_form_id . ' Field ' . $stlcf_idx . ' Label', $stlcf_field['label'] );
                        $field_key = strtolower( preg_replace( '/[^a-z0-9]/i', '_', trim( $stlcf_field['label'] ) ) );
                        
                        $prefill_val = '';
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        if ( isset( $_GET[ $field_key ] ) ) {
                            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                            $prefill_val = sanitize_text_field( wp_unslash( $_GET[ $field_key ] ) );
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                        } elseif ( isset( $_GET[ str_replace( '_', '-', $field_key ) ] ) ) {
                            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                            $prefill_val = sanitize_text_field( wp_unslash( $_GET[ str_replace( '_', '-', $field_key ) ] ) );
                        }
                        
                        if ( empty( $prefill_val ) && is_user_logged_in() ) {
                            $current_user = wp_get_current_user();
                            if ( strpos( $field_key, 'email' ) !== false ) {
                                $prefill_val = $current_user->user_email;
                            } elseif ( strpos( $field_key, 'name' ) !== false || strpos( $field_key, 'first' ) !== false || strpos( $field_key, 'last' ) !== false ) {
                                if ( ! empty( $current_user->user_firstname ) || ! empty( $current_user->user_lastname ) ) {
                                    if ( strpos( $field_key, 'first' ) !== false ) {
                                        $prefill_val = $current_user->user_firstname;
                                    } elseif ( strpos( $field_key, 'last' ) !== false ) {
                                        $prefill_val = $current_user->user_lastname;
                                    } else {
                                        $prefill_val = trim( $current_user->user_firstname . ' ' . $current_user->user_lastname );
                                    }
                                } else {
                                    $prefill_val = $current_user->display_name;
                                }
                            }
                        }
                        ?>
                        <div class="<?php echo esc_attr( $group_class ); ?>" style="<?php echo esc_attr( $group_style ); ?>" <?php echo $cond_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                            <label for="<?php echo esc_attr( $stlcf_f_id ); ?>" class="stlcf-field-label">
                                <?php echo esc_html( $field_label ); ?>
                                <?php if ( $stlcf_f_req ) : ?>
                                    <span>*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'textarea' ) : ?>
                                <textarea id="<?php echo esc_attr( $stlcf_f_id ); ?>" data-field-key="<?php echo esc_attr( $field_key ); ?>" name="stlcf_input[<?php echo esc_attr( $field_label ); ?>]" rows="4" placeholder="<?php
                                    /* translators: %s: textarea field label lowercased name */
                                    echo esc_attr( sprintf( __( 'Enter your %s...', 'sanirtech-lead-chat-forms' ), strtolower( $field_label ) ) ); 
                                ?>" <?php echo $stlcf_f_req ? 'required aria-required="true"' : ''; ?>><?php echo esc_textarea( $prefill_val ); ?></textarea>
                            <?php elseif ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'file' ) : ?>
                                <input type="file" id="<?php echo esc_attr( $stlcf_f_id ); ?>" data-field-key="<?php echo esc_attr( $field_key ); ?>" name="stlcf_file_<?php echo esc_attr( $field_key ); ?>" <?php echo $stlcf_f_req ? 'required aria-required="true"' : ''; ?> style="padding: 6px 0; border: none; font-size: 14px;">
                            <?php elseif ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'signature' ) : ?>
                                <div class="stlcf-signature-container" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:10px; width:100%; box-sizing:border-box; margin-top: 5px;">
                                    <canvas id="stlcf-sig-canvas-<?php echo esc_attr( $stlcf_f_id ); ?>" class="stlcf-signature-canvas" width="400" height="150" style="border:1px dashed #94a3b8; background:#fff; border-radius:4px; width:100%; height:150px; cursor:crosshair; touch-action:none;"></canvas>
                                    <div style="margin-top:6px; display:flex; justify-content:space-between; align-items:center;">
                                        <button type="button" class="button stlcf-clear-sig-btn" data-canvas="stlcf-sig-canvas-<?php echo esc_attr( $stlcf_f_id ); ?>" style="font-size:11px; padding:4px 8px; height:auto; line-height:1; font-weight:600; border-color:#dc2626; color:#dc2626; background:#fff; border-style:solid; border-width:1px; border-radius:4px; cursor:pointer;"><?php esc_html_e( 'Clear Signature', 'sanirtech-lead-chat-forms' ); ?></button>
                                        <span style="font-size:11px; color:#64748b;"><?php esc_html_e( 'Sign above inside the box', 'sanirtech-lead-chat-forms' ); ?></span>
                                    </div>
                                    <input type="hidden" id="stlcf-sig-data-<?php echo esc_attr( $stlcf_f_id ); ?>" name="stlcf_input[<?php echo esc_attr( $field_label ); ?>]" value="" <?php echo $stlcf_f_req ? 'required data-sig-req="1"' : ''; ?>>
                                </div>
                            <?php elseif ( isset( $stlcf_field['type'] ) && $stlcf_field['type'] === 'agent_select' ) : ?>
                                <select id="<?php echo esc_attr( $stlcf_f_id ); ?>" data-field-key="<?php echo esc_attr( $field_key ); ?>" name="stlcf_input[<?php echo esc_attr( $field_label ); ?>]" <?php echo $stlcf_f_req ? 'required aria-required="true"' : ''; ?>>
                                    <option value="" selected disabled><?php esc_html_e( 'Choose Department / Agent...', 'sanirtech-lead-chat-forms' ); ?></option>
                                    <?php
                                    $stlcf_lines = isset( $stlcf_field['routing'] ) ? explode( "\n", $stlcf_field['routing'] ) : array();
                                    foreach ( $stlcf_lines as $line_idx => $stlcf_line ) {
                                        $stlcf_line = trim( $stlcf_line );
                                        if ( empty( $stlcf_line ) ) { continue; }
                                        $stlcf_parts = explode( '|', $stlcf_line );
                                        $stlcf_name  = isset( $stlcf_parts[0] ) ? trim( $stlcf_parts[0] ) : '';
                                        $stlcf_phone = isset( $stlcf_parts[1] ) ? preg_replace( '/[^0-9]/', '', $stlcf_parts[1] ) : '';
                                        if ( ! empty( $stlcf_name ) && ! empty( $stlcf_phone ) ) {
                                            $translated_name = $this->translate_single_string( 'Form ' . $stlcf_form_id . ' Field ' . $stlcf_idx . ' Agent Name ' . $line_idx, $stlcf_name );
                                            $is_selected = ( strtolower( $stlcf_name ) === strtolower( $prefill_val ) );
                                            echo '<option value="' . esc_attr( $translated_name . '|' . $stlcf_phone ) . '" ' . selected( $is_selected, true, false ) . '>' . esc_html( $translated_name ) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            <?php else : 
                                // ==========================================
                                // SMART PHONE DETECTION LOGIC
                                // ==========================================
                                $stlcf_lbl_lower = strtolower( $field_label );
                                
                                // Check if the label contains phone-related keywords OR if the admin selected "Number" type
                                $stlcf_is_phone = ( strpos( $stlcf_lbl_lower, 'phone' ) !== false || strpos( $stlcf_lbl_lower, 'mobile' ) !== false || strpos( $stlcf_lbl_lower, 'whatsapp' ) !== false || ( isset($stlcf_field['type']) && $stlcf_field['type'] === 'number' ) );
                                
                                // Dynamically assign input type and our special JS tracking class
                                $stlcf_i_type  = $stlcf_is_phone ? 'tel' : ( ( isset( $stlcf_field['type'] ) && in_array( $stlcf_field['type'], array( 'text', 'email', 'number' ) ) ) ? $stlcf_field['type'] : 'text' );
                                $stlcf_p_class = $stlcf_is_phone ? 'stlcf-smart-phone' : '';
                                
                                // Determine semantic autocompletes for accessibility and SEO
                                $stlcf_auto_tag = '';
                                if ( strpos( $stlcf_lbl_lower, 'name' ) !== false ) {
                                    $stlcf_auto_tag = 'autocomplete="name"';
                                } elseif ( $stlcf_i_type === 'email' ) {
                                    $stlcf_auto_tag = 'autocomplete="email"';
                                } elseif ( $stlcf_i_type === 'tel' ) {
                                    $stlcf_auto_tag = 'autocomplete="tel"';
                                }
                                // ====================================== ?>
                                <input type="<?php echo esc_attr( $stlcf_i_type ); ?>" class="<?php echo esc_attr( $stlcf_p_class ); ?>" id="<?php echo esc_attr( $stlcf_f_id ); ?>" data-field-key="<?php echo esc_attr( $field_key ); ?>" name="stlcf_input[<?php echo esc_attr( $field_label ); ?>]" value="<?php echo esc_attr( $prefill_val ); ?>" placeholder="<?php
                                    /* translators: %s: text field label lowercased name */
                                    echo esc_attr( sprintf( __( 'Enter your %s...', 'sanirtech-lead-chat-forms' ), strtolower( $field_label ) ) ); 
                                ?>" <?php echo $stlcf_auto_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $stlcf_f_req ? 'required aria-required="true"' : ''; ?>>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                ?>
                
                <?php if ( $stlcf_c_type === 'built_in' ) : 
                    $stlcf_n1 = wp_rand(1, 9); $stlcf_n2 = wp_rand(1, 9); $stlcf_c_hash = wp_hash( $stlcf_n1 + $stlcf_n2 );
                    ?>
                    <div class="stlcf-field-group">
                        <label class="stlcf-field-label">
                            <?php echo esc_html( sprintf( 
                                /* translators: 1: First math number, 2: Second math number */
                                __("Spam Protection: What is %1\$d + %2\$d? *", 'sanirtech-lead-chat-forms' ), 
                                $stlcf_n1, $stlcf_n2 
                            ) ); ?>
                        </label>
                        <input type="hidden" name="stlcf_captcha_hash" value="<?php echo esc_attr( $stlcf_c_hash ); ?>">
                        <input type="number" name="stlcf_captcha_ans" required>
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

                <div class="stlcf-btn-flex-row">
                    <?php if ( ! ( $stlcf_is_offline && $stlcf_offline_action === 'email_only' ) ) : ?>
                        <button type="submit" class="stlcf-submit-trigger" name="stlcf_submit_type" value="whatsapp" style="background-color:<?php echo esc_attr( $stlcf_wa_clr ); ?>; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>;">
                            <?php echo esc_html( $stlcf_wa_txt ); ?>
                        </button>
                    <?php endif; ?>
                    <?php if ( $stlcf_em_btn_en === '1' || ( $stlcf_is_offline && $stlcf_offline_action === 'email_only' ) ) : ?>
                        <button type="submit" class="stlcf-submit-trigger" name="stlcf_submit_type" value="email" style="background-color:<?php echo esc_attr( $stlcf_em_clr ); ?>; border-radius:<?php echo esc_attr( $stlcf_radius ); ?>;">
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

        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $stlcf_form_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_form_id ) );
        // phpcs:enable
        if ( ! $stlcf_form_data ) { wp_send_json_error( array( 'message' => esc_html__( 'Form configuration missing.', 'sanirtech-lead-chat-forms' ) ) ); }
        $stlcf_fields = maybe_unserialize( $stlcf_form_data->fields );

        // Process secure file uploads and base64 signature fields
        if ( is_array( $stlcf_fields ) ) {
            foreach ( $stlcf_fields as $fld ) {
                $fld_label = $fld['label'];
                
                if ( isset( $fld['type'] ) && $fld['type'] === 'file' ) {
                    $field_key = strtolower( preg_replace( '/[^a-z0-9]/i', '_', trim( $fld_label ) ) );
                    $input_name = 'stlcf_file_' . $field_key;
                    
                    // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    if ( isset( $_FILES[$input_name] ) && ! empty( $_FILES[$input_name]['name'] ) ) {
                        if ( ! function_exists( 'wp_handle_upload' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                        }
                        
                        $uploaded_file = $_FILES[$input_name];
                        // phpcs:enable
                        $allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx' );
                        $file_ext = strtolower( pathinfo( $uploaded_file['name'], PATHINFO_EXTENSION ) );
                        
                        if ( ! in_array( $file_ext, $allowed_extensions, true ) ) {
                            wp_send_json_error( array( 'message' => esc_html__( 'File type upload denied for security compliance.', 'sanirtech-lead-chat-forms' ) ) );
                        }
                        
                        $upload_overrides = array( 'test_form' => false );
                        $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );
                        
                        if ( $movefile && ! isset( $movefile['error'] ) ) {
                            $_POST['stlcf_input'][$fld_label] = $movefile['url'];
                        } else {
                            wp_send_json_error( array( 'message' => $movefile['error'] ) );
                        }
                    }
                } elseif ( isset( $fld['type'] ) && $fld['type'] === 'signature' ) {
                    $raw_sig = isset( $_POST['stlcf_input'][$fld_label] ) ? sanitize_text_field( wp_unslash( $_POST['stlcf_input'][$fld_label] ) ) : '';
                    if ( strpos( $raw_sig, 'data:image/png;base64,' ) === 0 ) {
                        $sig_data = str_replace( 'data:image/png;base64,', '', $raw_sig );
                        $sig_data = str_replace( ' ', '+', $sig_data );
                        $decoded_image = base64_decode( $sig_data );
                        
                        $upload_dir = wp_upload_dir();
                        $filename = 'signature_' . uniqid() . '_' . time() . '.png';
                        $filepath = $upload_dir['path'] . '/' . $filename;
                        
                        if ( file_put_contents( $filepath, $decoded_image ) ) {
                            $_POST['stlcf_input'][$fld_label] = $upload_dir['url'] . '/' . $filename;
                        }
                    }
                }
            }
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Inputs are sanitized securely inside the subsequent foreach loop below.
        $stlcf_raw_inputs = wp_unslash( $_POST['stlcf_input'] );
        $stlcf_sanitized = array();
        $stlcf_dynamic_agent_phone = '';

        // Check if round-robin rotator is enabled for this form
        $rotator_active = isset( $stlcf_form_data->agent_rotator ) ? (int) $stlcf_form_data->agent_rotator : 0;
        if ( $rotator_active === 1 ) {
            $agents_list = isset( $stlcf_g_settings['multi_agents_list'] ) ? $stlcf_g_settings['multi_agents_list'] : array();
            if ( is_array( $agents_list ) && ! empty( $agents_list ) ) {
                $active_agents = array();
                foreach ( $agents_list as $ag ) {
                    $ag_phone = isset( $ag['phone'] ) ? preg_replace( '/[^0-9]/', '', $ag['phone'] ) : '';
                    if ( ! empty( $ag_phone ) ) {
                        $active_agents[] = $ag;
                    }
                }
                
                if ( ! empty( $active_agents ) ) {
                    $last_idx = (int) get_option( 'stlcf_last_rotator_idx_' . $stlcf_form_id, -1 );
                    $next_idx = ( $last_idx + 1 ) % count( $active_agents );
                    update_option( 'stlcf_last_rotator_idx_' . $stlcf_form_id, $next_idx );
                    
                    $selected_agent = $active_agents[$next_idx];
                    $stlcf_dynamic_agent_phone = preg_replace( '/[^0-9]/', '', $selected_agent['phone'] );
                    $stlcf_sanitized['Assigned Agent'] = isset( $selected_agent['name'] ) ? sanitize_text_field( $selected_agent['name'] ) : 'Round-Robin';
                }
            }
        }

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
                if ( ! empty( $stlcf_query_vars['utm_content'] ) ) { $stlcf_tracking_payload['UTM Content'] = sanitize_text_field( $stlcf_query_vars['utm_content'] ); }
                if ( ! empty( $stlcf_query_vars['utm_term'] ) ) { $stlcf_tracking_payload['UTM Term'] = sanitize_text_field( $stlcf_query_vars['utm_term'] ); }
            }
            
            foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term' ) as $utm_key ) {
                $post_key = 'stlcf_utm_' . $utm_key;
                if ( ! empty( $_POST[$post_key] ) ) {
                    $stlcf_tracking_payload[ ucwords( str_replace( '_', ' ', $utm_key ) ) ] = sanitize_text_field( wp_unslash( $_POST[$post_key] ) );
                }
            }
            
            if ( ! empty( $_POST['stlcf_referrer_url'] ) ) {
                $stlcf_tracking_payload['Referrer Source'] = esc_url_raw( wp_unslash( $_POST['stlcf_referrer_url'] ) );
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

        if ( $stlcf_webhook_enabled === '1' && ! empty( $stlcf_webhook_url ) ) {
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

        // Process conditional webhook rules overrides
        $form_webhook_rules = isset( $stlcf_form_data->webhook_rules ) ? $stlcf_form_data->webhook_rules : '';
        if ( ! empty( $form_webhook_rules ) ) {
            $rules_lines = explode( "\n", $form_webhook_rules );
            foreach ( $rules_lines as $rule_line ) {
                $rule_line = trim( $rule_line );
                if ( empty( $rule_line ) ) { continue; }
                $rule_parts = explode( '|', $rule_line );
                $field_lbl = isset( $rule_parts[0] ) ? trim( $rule_parts[0] ) : '';
                $match_val = isset( $rule_parts[1] ) ? trim( $rule_parts[1] ) : '';
                $target_url = isset( $rule_parts[2] ) ? esc_url_raw( trim( $rule_parts[2] ) ) : '';

                if ( ! empty( $field_lbl ) && ! empty( $match_val ) && ! empty( $target_url ) ) {
                    $matched = false;
                    foreach ( $stlcf_sanitized as $s_lbl => $s_val ) {
                        if ( strtolower( trim( $s_lbl ) ) === strtolower( $field_lbl ) && strtolower( trim( $s_val ) ) === strtolower( $match_val ) ) {
                            $matched = true;
                            break;
                        }
                    }
                    if ( $matched ) {
                        wp_remote_post( $target_url, array(
                            'method'      => 'POST',
                            'timeout'     => 5,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking'    => false,
                            'headers'     => array(
                                'Content-Type' => 'application/json; charset=utf-8'
                            ),
                            'body'        => wp_json_encode( $stlcf_webhook_payload ),
                            'cookies'     => array()
                        ));
                    }
                }
            }
        }

        // ======================================================================
        // 🔌 CRM & EMAIL MARKETING INTEGRATIONS (MAILCHIMP & HUBSPOT)
        // ======================================================================
        $user_email = '';
        $user_phone = '';
        $first_name = '';
        $last_name  = '';

        foreach ( $stlcf_sanitized as $k => $v ) {
            if ( empty( $user_email ) && is_email( $v ) ) {
                $user_email = $v;
            }
            $k_lower = strtolower( $k );
            if ( strpos( $k_lower, 'name' ) !== false ) {
                if ( empty( $first_name ) ) {
                    $first_name = $v;
                } else {
                    $last_name = $v;
                }
            }
            if ( empty( $user_phone ) && ( strpos( $k_lower, 'phone' ) !== false || strpos( $k_lower, 'mobile' ) !== false || strpos( $k_lower, 'whatsapp' ) !== false || strpos( $k_lower, 'tel' ) !== false ) ) {
                $user_phone = $v;
            }
        }

        // Mailchimp
        $mailchimp_enabled = isset( $stlcf_g_settings['enable_mailchimp'] ) ? $stlcf_g_settings['enable_mailchimp'] : '0';
        $mailchimp_key     = isset( $stlcf_g_settings['mailchimp_api_key'] ) ? trim( $stlcf_g_settings['mailchimp_api_key'] ) : '';
        $mailchimp_list    = isset( $stlcf_g_settings['mailchimp_list_id'] ) ? trim( $stlcf_g_settings['mailchimp_list_id'] ) : '';

        if ( $mailchimp_enabled === '1' && ! empty( $mailchimp_key ) && ! empty( $mailchimp_list ) && ! empty( $user_email ) ) {
            $mc_dc = 'us1';
            $key_parts = explode( '-', $mailchimp_key );
            if ( isset( $key_parts[1] ) ) {
                $mc_dc = $key_parts[1];
            }
            
            $mc_url = 'https://' . $mc_dc . '.api.mailchimp.com/3.0/lists/' . $mailchimp_list . '/members';
            $mc_payload = array(
                'email_address' => $user_email,
                'status'        => 'subscribed',
                'merge_fields'  => array(
                    'FNAME' => $first_name,
                    'LNAME' => $last_name,
                    'PHONE' => $user_phone
                )
            );

            $mc_headers = array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'apikey ' . $mailchimp_key
            );
            $mc_response = wp_remote_post( $mc_url, array(
                'method'      => 'POST',
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => $mc_headers,
                'body'        => wp_json_encode( $mc_payload )
            ));
            
            if ( is_wp_error( $mc_response ) || wp_remote_retrieve_response_code( $mc_response ) >= 400 ) {
                $this->queue_failed_crm_sync( 'mailchimp', $mc_url, $mc_payload, $mc_headers );
            }
        }

        // HubSpot
        $hubspot_enabled = isset( $stlcf_g_settings['enable_hubspot'] ) ? $stlcf_g_settings['enable_hubspot'] : '0';
        $hubspot_token   = isset( $stlcf_g_settings['hubspot_access_token'] ) ? trim( $stlcf_g_settings['hubspot_access_token'] ) : '';

        if ( $hubspot_enabled === '1' && ! empty( $hubspot_token ) && ( ! empty( $user_email ) || ! empty( $user_phone ) ) ) {
            $hs_url = 'https://api.hubapi.com/crm/v3/objects/contacts';
            
            $hs_properties = array(
                'firstname' => $first_name,
                'lastname'  => $last_name
            );
            if ( ! empty( $user_email ) ) {
                $hs_properties['email'] = $user_email;
            }
            if ( ! empty( $user_phone ) ) {
                $hs_properties['phone'] = $user_phone;
            }

            $hs_payload = array(
                'properties' => $hs_properties
            );

            $hs_headers = array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $hubspot_token
            );
            $hs_response = wp_remote_post( $hs_url, array(
                'method'      => 'POST',
                'timeout'     => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'headers'     => $hs_headers,
                'body'        => wp_json_encode( $hs_payload )
            ));
            
            if ( is_wp_error( $hs_response ) || wp_remote_retrieve_response_code( $hs_response ) >= 400 ) {
                $this->queue_failed_crm_sync( 'hubspot', $hs_url, $hs_payload, $hs_headers );
            }
        }
        // ======================================================================

        global $wpdb;
        $stlcf_sv_db  = isset( $stlcf_g_settings['save_to_db'] ) ? $stlcf_g_settings['save_to_db'] : '1';
        if ( $stlcf_sv_db === '1' ) {
            $stlcf_t_ent = $wpdb->prefix . 'stlcf_entries';
            $stlcf_sanitized['Submission Channel'] = ( $stlcf_s_mod === 'email' ? 'Email' : 'WhatsApp' );
            $stlcf_db_store = array_merge( $stlcf_sanitized, $stlcf_tracking_payload );
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->insert( $stlcf_t_ent, array( 'form_id' => $stlcf_form_id, 'form_data' => maybe_serialize( $stlcf_db_store ), 'page_url' => $stlcf_pg_url, 'submitted_at' => current_time( 'mysql' ) ), array( '%d', '%s', '%s', '%s' ) );
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}stlcf_forms SET conversions = conversions + 1 WHERE id = %d", $stlcf_form_id ) );
            
            $table_abandoned = $wpdb->prefix . 'stlcf_abandoned_leads';
            $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
            $wpdb->delete( $table_abandoned, array( 'form_id' => $stlcf_form_id, 'ip_address' => $ip_address ), array( '%d', '%s' ) );
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
            
            // Check form-level conditional email routing rules overrides
            $form_email_rules = isset( $stlcf_form_data->email_rules ) ? $stlcf_form_data->email_rules : '';
            if ( ! empty( $form_email_rules ) ) {
                $e_rules_lines = explode( "\n", $form_email_rules );
                foreach ( $e_rules_lines as $e_rule_line ) {
                    $e_rule_line = trim( $e_rule_line );
                    if ( empty( $e_rule_line ) ) { continue; }
                    $e_rule_parts = explode( '|', $e_rule_line );
                    $e_field_lbl = isset( $e_rule_parts[0] ) ? trim( $e_rule_parts[0] ) : '';
                    $e_match_val = isset( $e_rule_parts[1] ) ? trim( $e_rule_parts[1] ) : '';
                    $e_target_email = isset( $e_rule_parts[2] ) ? sanitize_email( trim( $e_rule_parts[2] ) ) : '';

                    if ( ! empty( $e_field_lbl ) && ! empty( $e_match_val ) && is_email( $e_target_email ) ) {
                        foreach ( $stlcf_sanitized as $s_lbl => $s_val ) {
                            if ( strtolower( trim( $s_lbl ) ) === strtolower( $e_field_lbl ) && strtolower( trim( $s_val ) ) === strtolower( $e_match_val ) ) {
                                $stlcf_rec_mb = $e_target_email;
                                break 2;
                            }
                        }
                    }
                }
            }

            $stlcf_em_sub = esc_html__( 'New Form Lead Submission', 'sanirtech-lead-chat-forms' );
            
            $fields_html = '';
            foreach ( $stlcf_sanitized as $lbl => $val ) {
                $fields_html .= sprintf( 
                    '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:10px; font-weight:600; color:#475569; font-size:13px; width:40%%; text-align:left;">%s</td><td style="padding:10px; color:#1e293b; font-size:13px; text-align:left;">%s</td></tr>',
                    esc_html( $lbl ),
                    esc_html( $val )
                );
            }
            
            $tracking_html = '';
            if ( ! empty( $stlcf_tracking_payload ) ) {
                foreach ( $stlcf_tracking_payload as $lbl => $val ) {
                    $tracking_html .= sprintf( 
                        '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 10px; font-weight:500; color:#64748b; font-size:12px; width:40%%; text-align:left;">%s</td><td style="padding:8px 10px; color:#475569; font-size:12px; text-align:left;">%s</td></tr>',
                        esc_html( $lbl ),
                        esc_html( $val )
                    );
                }
            }

            $stlcf_em_bdy = '
            <div style="background-color:#f8fafc; padding:20px; font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif; line-height:1.5;">
                <div style="max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="background-color:#1e293b; padding:20px; text-align:center; color:#ffffff;">
                        <h2 style="margin:0; font-size:20px; font-weight:700; color:#ffffff;">' . esc_html__( 'Lead Capture Receipt', 'sanirtech-lead-chat-forms' ) . '</h2>
                        <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8; color:#f1f5f9;">' . 
                        /* translators: %s: captured page URL address */
                        sprintf( esc_html__( 'Captured from: %s', 'sanirtech-lead-chat-forms' ), esc_url( $stlcf_pg_url ) ) . '</p>
                    </div>
                    <div style="padding:25px;">
                        <h4 style="margin:0 0 15px 0; color:#0f172a; border-bottom:2px solid #f1f5f9; padding-bottom:8px; font-size:14px; text-transform:uppercase; letter-spacing:0.05em; text-align:left;">' . esc_html__( 'Submitted Lead Data', 'sanirtech-lead-chat-forms' ) . '</h4>
                        <table style="width:100%; border-collapse:collapse; margin-bottom:25px;">
                            ' . $fields_html . '
                        </table>
            ';
            
            if ( ! empty( $tracking_html ) ) {
                $stlcf_em_bdy .= '
                        <h4 style="margin:0 0 15px 0; color:#0f172a; border-bottom:2px solid #f1f5f9; padding-bottom:8px; font-size:14px; text-transform:uppercase; letter-spacing:0.05em; text-align:left;">' . esc_html__( 'Visitor & Campaign Context', 'sanirtech-lead-chat-forms' ) . '</h4>
                        <table style="width:100%; border-collapse:collapse;">
                            ' . $tracking_html . '
                        </table>
                ';
            }

            $stlcf_em_bdy .= '
                    </div>
                    <div style="background-color:#f1f5f9; padding:15px; text-align:center; font-size:11px; color:#64748b;">
                        ' . esc_html__( 'This is an automated notification from Direct WhatsApp Lead Forms.', 'sanirtech-lead-chat-forms' ) . '
                    </div>
                </div>
            </div>';

            $headers = array( 'Content-Type: text/html; charset=UTF-8' );
            wp_mail( $stlcf_rec_mb, $stlcf_em_sub, $stlcf_em_bdy, $headers );

            $stlcf_ar_active = isset( $stlcf_g_settings['enable_auto_responder'] ) ? $stlcf_g_settings['enable_auto_responder'] : '0';
            $stlcf_form_ar_enabled = isset( $stlcf_form_data->autoresponder_enabled ) ? (int) $stlcf_form_data->autoresponder_enabled : 0;
            
            if ( $stlcf_form_ar_enabled === 1 || $stlcf_ar_active === '1' ) {
                $stlcf_client_target_email = '';
                $stlcf_client_extracted_name = 'Customer';

                foreach ( $stlcf_sanitized as $s_lbl => $s_val ) {
                    $s_lbl_clean = strtolower( trim( $s_lbl ) );
                    if ( is_email( $s_val ) ) { $stlcf_client_target_email = sanitize_email( $s_val ); }
                    if ( strpos( $s_lbl_clean, 'name' ) !== false || strpos( $s_lbl_clean, 'naam' ) !== false ) { $stlcf_client_extracted_name = sanitize_text_field( $s_val ); }
                }

                if ( ! empty( $stlcf_client_target_email ) ) {
                    if ( $stlcf_form_ar_enabled === 1 ) {
                        $stlcf_ar_subject_blueprint = isset( $stlcf_form_data->autoresponder_subject ) ? sanitize_text_field( $stlcf_form_data->autoresponder_subject ) : 'Acknowledgement Notice';
                        $stlcf_ar_message_blueprint = isset( $stlcf_form_data->autoresponder_message ) ? $this->rows_textarea_clean_escape( $stlcf_form_data->autoresponder_message ) : '';
                    } else {
                        $stlcf_ar_subject_blueprint = isset( $stlcf_g_settings['auto_responder_subject'] ) ? sanitize_text_field( $stlcf_g_settings['auto_responder_subject'] ) : 'Acknowledgement Notice';
                        $stlcf_ar_message_blueprint = isset( $stlcf_g_settings['auto_responder_message'] ) ? $this->rows_textarea_clean_escape( $stlcf_g_settings['auto_responder_message'] ) : '';
                    }
                    
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
            if ( isset( $stlcf_form_data->whatsapp_override ) && ! empty( $stlcf_form_data->whatsapp_override ) ) {
                $stlcf_base_phone = preg_replace( '/[^0-9]/', '', $stlcf_form_data->whatsapp_override );
            }
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
        $stlcf_logo_id = get_theme_mod( 'custom_logo' );
        $stlcf_logo_url = $stlcf_logo_id ? wp_get_attachment_image_url( $stlcf_logo_id, 'full' ) : get_site_icon_url();
        $stlcf_desc = get_bloginfo( 'description' );

        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Organization',
            '@id'         => esc_url( home_url( '/' ) ) . '#stlcf-organization',
            'name'        => esc_html( get_bloginfo( 'name' ) ),
            'url'         => esc_url( home_url( '/' ) ),
            'description' => esc_html( ! empty( $stlcf_desc ) ? $stlcf_desc : __( 'Professional Business Organization', 'sanirtech-lead-chat-forms' ) ),
            'contactPoint' => array(
                '@type'       => 'ContactPoint',
                'telephone'   => '+' . $stlcf_base_phone,
                'contactType' => 'customer service',
                'url'         => 'https://wa.me/' . $stlcf_base_phone,
                'availableLanguage' => array( 'English' ),
                'areaServed'  => 'Worldwide'
            )
        );

        if ( ! empty( $stlcf_logo_url ) ) {
            $schema['logo'] = esc_url( $stlcf_logo_url );
            $schema['image'] = esc_url( $stlcf_logo_url );
        }

        return $schema;
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
        $stlcf_tz_target = ! empty( $stlcf_g_settings['business_timezone'] ) ? $stlcf_g_settings['business_timezone'] : 'UTC';
        try {
            $stlcf_tz_object = new DateTimeZone( $stlcf_tz_target );
            $stlcf_time_now  = new DateTime( 'now', $stlcf_tz_object );
        } catch ( Exception $e ) {
            return false;
        }

        $stlcf_day_slug = strtolower( $stlcf_time_now->format( 'l' ) ); 
        $stlcf_clock_check = $stlcf_time_now->format( 'H:i' );
        $stlcf_days_approved = isset( $stlcf_g_settings['business_days'] ) ? $stlcf_g_settings['business_days'] : array();
        if ( ! is_array( $stlcf_days_approved ) || ! isset( $stlcf_days_approved[$stlcf_day_slug] ) || $stlcf_days_approved[$stlcf_day_slug] !== '1' ) { return true; }
        $stlcf_start_frame = isset( $stlcf_g_settings['business_start'] ) ? $stlcf_g_settings['business_start'] : '09:00';
        $stlcf_end_frame   = isset( $stlcf_g_settings['business_end'] ) ? $stlcf_g_settings['business_end'] : '18:00';
        if ( $stlcf_clock_check < $stlcf_start_frame || $stlcf_clock_check > $stlcf_end_frame ) { return true; }
        return false;
    }

    private function translate_single_string( $name, $default_value ) {
        if ( function_exists( 'icl_t' ) ) {
            return icl_t( 'sanirtech-lead-chat-forms', $name, $default_value );
        }
        if ( function_exists( 'pll__' ) ) {
            return pll__( $default_value );
        }
        return $default_value;
    }

    public function register_gutenberg_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        
        register_block_type( 'sanirtech-lead-chat-forms/form-select', array(
            'attributes'      => array(
                'formId' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
            ),
            'render_callback' => array( $this, 'render_gutenberg_block_callback' ),
        ) );
    }

    public function render_gutenberg_block_callback( $attributes ) {
        $form_id = isset( $attributes['formId'] ) ? intval( $attributes['formId'] ) : 0;
        if ( empty( $form_id ) ) {
            return '';
        }
        return $this->render_dynamic_form( array( 'id' => $form_id ) );
    }

    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'stlcf-gutenberg-block',
            STLCF_PLUGIN_URL . 'assets/admin/js/stlcf-block.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
            STLCF_VERSION,
            true
        );
        
        global $wpdb;
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $forms = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}stlcf_forms ORDER BY title ASC", ARRAY_A );
        // phpcs:enable
        
        wp_localize_script( 'stlcf-gutenberg-block', 'stlcf_block_forms_list', $forms );
    }

    public function handle_abandoned_lead_logging() {
        // Run nonce verification
        if ( ! isset( $_POST['stlcf_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_nonce'] ) ), 'stlcf_form_submission_security_nonce' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) ) );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( wp_unslash( $_POST['form_id'] ) ) : 0;
        if ( $form_id <= 0 ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid Form ID', 'sanirtech-lead-chat-forms' ) ) );
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $raw_inputs = isset( $_POST['stlcf_input'] ) ? wp_unslash( $_POST['stlcf_input'] ) : array();
        $sanitized_fields = array();
        if ( is_array( $raw_inputs ) ) {
            foreach ( $raw_inputs as $lbl => $val ) {
                if ( strpos( $val, 'data:image/' ) === 0 ) {
                    continue;
                }
                $sanitized_fields[sanitize_text_field( $lbl )] = sanitize_textarea_field( $val );
            }
        }
        
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $utm_data = isset( $_POST['utm_data'] ) ? (array) wp_unslash( $_POST['utm_data'] ) : array();
        foreach ( $utm_data as $key => $val ) {
            $lbl = ucwords( str_replace( '_', ' ', $key ) );
            $sanitized_fields[$lbl] = sanitize_text_field( $val );
        }
        if ( ! empty( $_POST['referrer_url'] ) ) {
            $sanitized_fields['Referrer Source'] = esc_url_raw( wp_unslash( $_POST['referrer_url'] ) );
        }

        $has_data = false;
        foreach ( $sanitized_fields as $lbl => $val ) {
            if ( ! empty( $val ) ) {
                $has_data = true;
                break;
            }
        }

        if ( ! $has_data ) {
            wp_send_json_success( array( 'message' => 'No inputs logged yet.' ) );
        }

        $page_url = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
        $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

        global $wpdb;
        $table_abandoned = esc_sql( $wpdb->prefix . 'stlcf_abandoned_leads' );

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$table_abandoned} WHERE form_id = %d AND ip_address = %s AND updated_at >= %s",
            $form_id,
            $ip_address,
            gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS )
        ) );

        if ( $existing ) {
            $wpdb->update(
                $table_abandoned,
                array(
                    'form_data'  => maybe_serialize( $sanitized_fields ),
                    'page_url'   => $page_url,
                    'updated_at' => current_time( 'mysql' )
                ),
                array( 'id' => $existing->id ),
                array( '%s', '%s', '%s' ),
                array( '%d' )
            );
        } else {
            $wpdb->insert(
                $table_abandoned,
                array(
                    'form_id'    => $form_id,
                    'form_data'  => maybe_serialize( $sanitized_fields ),
                    'page_url'   => $page_url,
                    'ip_address' => $ip_address,
                    'updated_at' => current_time( 'mysql' )
                ),
                array( '%d', '%s', '%s', '%s', '%s' )
            );
        }
        // phpcs:enable

        wp_send_json_success( array( 'message' => 'Progress saved.' ) );
    }

    private function queue_failed_crm_sync( $platform, $url, $payload, $headers ) {
        $queue = get_option( 'stlcf_failed_crm_syncs_queue', array() );
        if ( ! is_array( $queue ) ) {
            $queue = array();
        }
        $queue[] = array(
            'platform' => $platform,
            'url'      => $url,
            'payload'  => $payload,
            'headers'  => $headers,
            'added_at' => time()
        );
        update_option( 'stlcf_failed_crm_syncs_queue', $queue );
    }

    private function get_visitor_country() {
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        if ( empty( $ip ) || $ip === '127.0.0.1' || $ip === '::1' || $ip === '127.0.0.9' ) {
            return 'US';
        }

        $transient_key = 'stlcf_ip_country_' . md5( $ip );
        $cached_country = get_transient( $transient_key );
        if ( false !== $cached_country ) {
            return $cached_country;
        }

        $response = wp_remote_get( 'http://ip-api.com/json/' . $ip, array( 'timeout' => 2 ) );
        if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( ! empty( $body['countryCode'] ) ) {
                $country = strtoupper( sanitize_text_field( $body['countryCode'] ) );
                set_transient( $transient_key, $country, DAY_IN_SECONDS );
                return $country;
            }
        }

        return 'US';
    }

    public function render_woo_product_whatsapp_button() {
        $stlcf_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_woo_enabled = isset( $stlcf_settings['enable_woo_button'] ) ? $stlcf_settings['enable_woo_button'] : '0';
        if ( $stlcf_woo_enabled !== '1' ) {
            return;
        }

        $stlcf_global_ph = isset( $stlcf_settings['global_phone'] ) ? preg_replace( '/[^0-9]/', '', $stlcf_settings['global_phone'] ) : '';
        if ( empty( $stlcf_global_ph ) ) {
            return;
        }

        global $product;
        if ( ! $product ) {
            return;
        }

        $product_title = $product->get_name();
        $product_url = get_permalink( $product->get_id() );

        $stlcf_woo_btn_txt = isset( $stlcf_settings['woo_button_text'] ) ? $stlcf_settings['woo_button_text'] : __( 'Inquire on WhatsApp', 'sanirtech-lead-chat-forms' );
        $stlcf_woo_btn_msg = isset( $stlcf_settings['woo_button_message'] ) ? $stlcf_settings['woo_button_message'] : 'Hi! I have a question about [Product Title] ([Product URL]). Can you please help?';

        // Parse placeholders
        $stlcf_parsed_msg = str_replace(
            array( '[Product Title]', '[Product URL]' ),
            array( $product_title, $product_url ),
            $stlcf_woo_btn_msg
        );

        $stlcf_wa_url = "https://wa.me/" . esc_attr( $stlcf_global_ph ) . "?text=" . rawurlencode( $stlcf_parsed_msg );
        $stlcf_fl_clr = isset( $stlcf_settings['float_btn_color'] ) ? sanitize_hex_color( $stlcf_settings['float_btn_color'] ) : '#25D366';
        
        ?>
        <a href="<?php echo esc_url( $stlcf_wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="button stlcf-woo-whatsapp-btn" style="background-color: <?php echo esc_attr( $stlcf_fl_clr ); ?> !important; color: #ffffff !important; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-left: 10px; border: none; vertical-align: middle; line-height: 1.5;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width:16px; height:16px; fill:#ffffff; display: inline-block; vertical-align: middle; margin: 0 4px 0 0;"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.8-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
            <?php echo esc_html( $stlcf_woo_btn_txt ); ?>
        </a>
        <?php
    }
}