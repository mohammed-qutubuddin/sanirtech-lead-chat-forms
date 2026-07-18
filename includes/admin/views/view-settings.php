<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$stlcf_options = get_option( 'stlcf_general_settings', array() );

// General & Database Defaults
$stlcf_g_ph       = isset( $stlcf_options['global_phone'] ) ? $stlcf_options['global_phone'] : '';
$stlcf_sv_db      = isset( $stlcf_options['save_to_db'] ) ? $stlcf_options['save_to_db'] : '1';
$stlcf_adv_track  = isset( $stlcf_options['enable_advanced_tracking'] ) ? $stlcf_options['enable_advanced_tracking'] : '1';
$stlcf_seo_schema = isset( $stlcf_options['enable_seo_schema'] ) ? $stlcf_options['enable_seo_schema'] : '1';

// GDPR Compliance Option Defaults
$stlcf_gdpr_en   = isset( $stlcf_options['enable_gdpr'] ) ? $stlcf_options['enable_gdpr'] : '0';
$stlcf_gdpr_txt  = isset( $stlcf_options['gdpr_text'] ) ? $stlcf_options['gdpr_text'] : __( 'I consent to having this website store my submitted information so they can respond to my inquiry via WhatsApp.', 'sanirtech-lead-chat-forms' );
$stlcf_gdpr_page = isset( $stlcf_options['gdpr_privacy_page'] ) ? $stlcf_options['gdpr_privacy_page'] : '';

// Styling Defaults
$stlcf_btn_d  = isset( $stlcf_options['button_design'] ) ? $stlcf_options['button_design'] : 'style_flat';
$stlcf_f_sz   = isset( $stlcf_options['font_size'] ) ? $stlcf_options['font_size'] : '16';
$stlcf_wa_txt = isset( $stlcf_options['wa_btn_text'] ) ? $stlcf_options['wa_btn_text'] : 'Submit via WhatsApp';

// Email Settings & Auto-Responder Defaults
$stlcf_e_btn = isset( $stlcf_options['enable_email_btn'] ) ? $stlcf_options['enable_email_btn'] : '0';
$stlcf_e_txt = isset( $stlcf_options['email_btn_text'] ) ? $stlcf_options['email_btn_text'] : 'Submit via Email';
$stlcf_a_rec = isset( $stlcf_options['admin_email_receiver'] ) ? $stlcf_options['admin_email_receiver'] : get_option( 'admin_email' );
$stlcf_ar_en  = isset( $stlcf_options['enable_auto_responder'] ) ? $stlcf_options['enable_auto_responder'] : '0';
$stlcf_ar_sub = isset( $stlcf_options['auto_responder_subject'] ) ? $stlcf_options['auto_responder_subject'] : 'Thank you for your enquiry!';
$stlcf_ar_msg = isset( $stlcf_options['auto_responder_message'] ) ? $stlcf_options['auto_responder_message'] : "Hi [Your Name],\r\n\r\nThank you for reaching out to us. We have received your query regarding [Form Title] and will get back to you as soon as possible.\r\n\r\nBest Regards,\r\nManagement Team";

// Independent Color Options
$stlcf_b_clr  = isset( $stlcf_options['btn_color'] ) ? $stlcf_options['btn_color'] : '#25D366';
$stlcf_e_clr  = isset( $stlcf_options['email_btn_color'] ) ? $stlcf_options['email_btn_color'] : '#1e293b';
$stlcf_fl_clr = isset( $stlcf_options['float_btn_color'] ) ? $stlcf_options['float_btn_color'] : '#25D366';

// Captcha Defaults
$stlcf_c_typ  = isset( $stlcf_options['captcha_type'] ) ? $stlcf_options['captcha_type'] : 'none';
$stlcf_ts_sk  = isset( $stlcf_options['turnstile_site_key'] ) ? $stlcf_options['turnstile_site_key'] : '';
$stlcf_ts_sec = isset( $stlcf_options['turnstile_secret_key'] ) ? $stlcf_options['turnstile_secret_key'] : '';
$stlcf_r2_sk  = isset( $stlcf_options['recaptcha_site_key'] ) ? $stlcf_options['recaptcha_site_key'] : '';
$stlcf_r2_sec = isset( $stlcf_options['recaptcha_secret_key'] ) ? $stlcf_options['recaptcha_secret_key'] : '';
$stlcf_r3_sk  = isset( $stlcf_options['recaptcha_v3_site_key'] ) ? $stlcf_options['recaptcha_v3_site_key'] : '';
$stlcf_r3_sec = isset( $stlcf_options['recaptcha_v3_secret_key'] ) ? $stlcf_options['recaptcha_v3_secret_key'] : '';

// Smart Routing State
$stlcf_agent_route = isset( $stlcf_options['enable_agent_routing'] ) ? $stlcf_options['enable_agent_routing'] : '1';

// Business Hours Defaults
$stlcf_hours_enabled = isset( $stlcf_options['enable_business_hours'] ) ? $stlcf_options['enable_business_hours'] : '0';
$stlcf_hours_tz      = isset( $stlcf_options['business_timezone'] ) ? $stlcf_options['business_timezone'] : get_option( 'timezone_string' );
$stlcf_hours_start   = isset( $stlcf_options['business_start'] ) ? $stlcf_options['business_start'] : '09:00';
$stlcf_hours_end     = isset( $stlcf_options['business_end'] ) ? $stlcf_options['business_end'] : '18:00';
$stlcf_offline_act   = isset( $stlcf_options['offline_action'] ) ? $stlcf_options['offline_action'] : 'show_notice';
$stlcf_offline_msg   = isset( $stlcf_options['offline_message'] ) ? $stlcf_options['offline_message'] : 'We are currently offline. Responses might be delayed.';
$stlcf_business_days = isset( $stlcf_options['business_days'] ) ? $stlcf_options['business_days'] : array( 'monday'=>'1', 'tuesday'=>'1', 'wednesday'=>'1', 'thursday'=>'1', 'friday'=>'1' );

// Analytics & Tracking Defaults
$stlcf_track_en    = isset( $stlcf_options['enable_pixels_tracking'] ) ? $stlcf_options['enable_pixels_tracking'] : '0';
$stlcf_fb_id       = isset( $stlcf_options['fb_pixel_id'] ) ? $stlcf_options['fb_pixel_id'] : '';
$stlcf_ga4_id      = isset( $stlcf_options['ga4_measurement_id'] ) ? $stlcf_options['ga4_measurement_id'] : '';
$stlcf_inject_base = isset( $stlcf_options['inject_base_scripts'] ) ? $stlcf_options['inject_base_scripts'] : '0';
$stlcf_pixel_event = isset( $stlcf_options['fb_pixel_event'] ) ? $stlcf_options['fb_pixel_event'] : 'Lead';

// Advanced Floating Widget Defaults
$stlcf_f_btn        = isset( $stlcf_options['floating_btn'] ) ? $stlcf_options['floating_btn'] : '0';
$stlcf_fw_tooltip   = isset( $stlcf_options['fw_tooltip_text'] ) ? $stlcf_options['fw_tooltip_text'] : 'Chat with us!';
$stlcf_fw_pos       = isset( $stlcf_options['fw_position'] ) ? $stlcf_options['fw_position'] : 'right';
$stlcf_fw_msg       = isset( $stlcf_options['fw_prefilled_msg'] ) ? $stlcf_options['fw_prefilled_msg'] : '';
$stlcf_fw_vis       = isset( $stlcf_options['fw_visibility'] ) ? $stlcf_options['fw_visibility'] : 'sitewide';
$stlcf_dashboard_en = isset( $stlcf_options['enable_analytics_dashboard'] ) ? $stlcf_options['enable_analytics_dashboard'] : '1';

$stlcf_gdpr_cron = isset( $stlcf_options['enable_gdpr_cron'] ) ? $stlcf_options['enable_gdpr_cron'] : '0';
$stlcf_gdpr_days = isset( $stlcf_options['gdpr_retention_days'] ) ? $stlcf_options['gdpr_retention_days'] : '30';

$stlcf_fw_exit  = isset( $stlcf_options['fw_exit_intent'] ) ? $stlcf_options['fw_exit_intent'] : '0';
$stlcf_fw_delay = isset( $stlcf_options['fw_time_delay'] ) ? $stlcf_options['fw_time_delay'] : '0';

// Webhooks & Integrations
$stlcf_enable_webhook = isset( $stlcf_options['enable_webhook'] ) ? $stlcf_options['enable_webhook'] : '0';
$stlcf_webhook_url    = isset( $stlcf_options['webhook_url'] ) ? $stlcf_options['webhook_url'] : '';
$stlcf_enable_mailchimp = isset( $stlcf_options['enable_mailchimp'] ) ? $stlcf_options['enable_mailchimp'] : '0';
$stlcf_mailchimp_api    = isset( $stlcf_options['mailchimp_api_key'] ) ? $stlcf_options['mailchimp_api_key'] : '';
$stlcf_mailchimp_list   = isset( $stlcf_options['mailchimp_list_id'] ) ? $stlcf_options['mailchimp_list_id'] : '';
$stlcf_enable_hubspot   = isset( $stlcf_options['enable_hubspot'] ) ? $stlcf_options['enable_hubspot'] : '0';
$stlcf_hubspot_token    = isset( $stlcf_options['hubspot_access_token'] ) ? $stlcf_options['hubspot_access_token'] : '';

// WooCommerce Options
$stlcf_woo_enabled = isset( $stlcf_options['enable_woo_button'] ) ? $stlcf_options['enable_woo_button'] : '0';
$stlcf_woo_btn_txt = isset( $stlcf_options['woo_button_text'] ) ? $stlcf_options['woo_button_text'] : __( 'Inquire on WhatsApp', 'sanirtech-lead-chat-forms' );
$stlcf_woo_btn_msg = isset( $stlcf_options['woo_button_message'] ) ? $stlcf_options['woo_button_message'] : 'Hi! I have a question about [Product Title] ([Product URL]). Can you please help?';

if ( empty( $stlcf_hours_tz ) ) { $stlcf_hours_tz = 'UTC'; }
?>

<div class="wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 24px;"><?php esc_html_e( 'Plugin Settings & Configurations', 'sanirtech-lead-chat-forms' ); ?></h1>
        <a href="admin.php?page=stlcf-wizard" class="button button-secondary" style="font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 32px; line-height: 1;">
            <span class="dashicons dashicons-forms" style="font-size: 16px; width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center; margin: 0;"></span>
            <span style="display: inline-block; line-height: 1;"><?php esc_html_e( 'Launch Onboarding Setup Wizard', 'sanirtech-lead-chat-forms' ); ?></span>
        </a>
    </div>
    
    <div class="stlcf-settings-container">
        <!-- Pro Vertical Sidebar navigation tabs -->
        <div class="stlcf-settings-sidebar">
            <a href="#general" class="stlcf-settings-sidebar-tab active" data-tab="stlcf-tab-general">
                <span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( 'General Settings', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#styling" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-styling">
                <span class="dashicons dashicons-art"></span><?php esc_html_e( 'Styling & Design', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#email" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-email">
                <span class="dashicons dashicons-email"></span><?php esc_html_e( 'Email Routing', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#routing" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-routing">
                <span class="dashicons dashicons-groups"></span><?php esc_html_e( 'Smart Routing', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#hours" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-hours">
                <span class="dashicons dashicons-clock"></span><?php esc_html_e( 'Business Hours', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#widget" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-widget">
                <span class="dashicons dashicons-format-chat"></span><?php esc_html_e( 'Floating Widget', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#analytics" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-analytics">
                <span class="dashicons dashicons-chart-bar"></span><?php esc_html_e( 'Analytics & Pixels', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#security" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-security">
                <span class="dashicons dashicons-shield"></span><?php esc_html_e( 'Spam Security', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#webhooks" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-webhooks">
                <span class="dashicons dashicons-admin-links"></span><?php esc_html_e( 'Webhooks & Zapier', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#integrations" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-integrations">
                <span class="dashicons dashicons-networking"></span><?php esc_html_e( 'CRM Integrations', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <a href="#woocommerce" class="stlcf-settings-sidebar-tab" data-tab="stlcf-tab-woocommerce">
                <span class="dashicons dashicons-cart"></span><?php esc_html_e( 'WooCommerce Settings', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            <!--
            <a href="#pro" class="stlcf-settings-sidebar-tab" style="background:#fef3c7; color:#b45309; font-weight:700; border-left:4px solid #b45309;" data-tab="stlcf-tab-pro">
                <span class="dashicons dashicons-star-filled" style="color:#b45309;"></span><?php esc_html_e( 'Go Pro / License Key', 'sanirtech-lead-chat-forms' ); ?>
            </a>
            -->
        </div>

        <!-- Right Content Cards -->
        <div class="stlcf-settings-content">
            <form method="post" action="options.php">
                <?php settings_fields( 'stlcf_settings_group' ); ?>

                <!-- Tab 1: General -->
                <div id="stlcf-tab-general" class="stlcf-tab-section stlcf-card">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'General Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Default WhatsApp Receiver Phone', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[global_phone]" value="<?php echo esc_attr( $stlcf_g_ph ); ?>" placeholder="e.g. 919999999999 (country code, no spaces/plus)" class="regular-text">
                                    <p class="description"><?php esc_html_e( 'Target number used for form redirections if no overrides are specified.', 'sanirtech-lead-chat-forms' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Save Leads to Database Log', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[save_to_db]" value="1" <?php checked( $stlcf_sv_db, '1' ); ?>> <?php esc_html_e( 'Record entries inside the local WordPress database.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Structured Schema Markup SEO', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[enable_seo_schema]" value="1" <?php checked( $stlcf_seo_schema, '1' ); ?>> <?php esc_html_e( 'Embed schema.org JSON-LD business contact schemas in page footers dynamically.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'GDPR Consent Requirement', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_gdpr" name="stlcf_general_settings[enable_gdpr]" value="1" <?php checked( $stlcf_gdpr_en, '1' ); ?>> <?php esc_html_e( 'Enforce privacy GDPR consent checkbox approval on all forms layout pages.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-gdpr-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'GDPR Consent Text Message', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <textarea name="stlcf_general_settings[gdpr_text]" rows="3" class="large-text"><?php echo esc_textarea( $stlcf_gdpr_txt ); ?></textarea>
                                </td>
                            </tr>
                            <tr class="stlcf-gdpr-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Privacy Policy Page Link', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php
                                    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
                                    $stlcf_dropdown = wp_dropdown_pages( array(
                                        'name'             => 'stlcf_general_settings[gdpr_privacy_page]',
                                        'show_option_none' => __( '-- Choose Privacy Policy Page --', 'sanirtech-lead-chat-forms' ),
                                        'selected'         => $stlcf_gdpr_page,
                                        'echo'             => 0,
                                    ) );
                                    // phpcs:enable
                                    echo wp_kses_post( $stlcf_dropdown );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'GDPR Auto-Clean Lead Logs', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_gdpr_cron" name="stlcf_general_settings[enable_gdpr_cron]" value="1" <?php checked( $stlcf_gdpr_cron, '1' ); ?>> <?php esc_html_e( 'Enable auto-deletion of old entries log data records to comply with GDPR storage limits.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-gdpr-cron-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Retention Interval Threshold (Days)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="number" name="stlcf_general_settings[gdpr_retention_days]" value="<?php echo esc_attr( $stlcf_gdpr_days ); ?>" min="1" class="small-text">
                                    <span class="description"><?php esc_html_e( 'Days to retain entries before permanent purging.', 'sanirtech-lead-chat-forms' ); ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Styling -->
                <div id="stlcf-tab-styling" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Styling & Design', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Button Borders Design Style', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select name="stlcf_general_settings[button_design]" style="min-width: 200px;">
                                        <option value="style_flat" <?php selected( $stlcf_btn_d, 'style_flat' ); ?>><?php esc_html_e( 'Modern Flat (Square edges)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="style_rounded" <?php selected( $stlcf_btn_d, 'style_rounded' ); ?>><?php esc_html_e( 'Rounded Corners (Slight curve)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="style_pill" <?php selected( $stlcf_btn_d, 'style_pill' ); ?>><?php esc_html_e( 'Pill Shapes (Circular edges)', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Global Base Font Size (Pixels)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="number" name="stlcf_general_settings[font_size]" value="<?php echo esc_attr( $stlcf_f_sz ); ?>" class="small-text" min="10" max="24">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'WhatsApp Button Color Accent', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="color" name="stlcf_general_settings[btn_color]" value="<?php echo esc_attr( $stlcf_b_clr ); ?>" class="stlcf-color-input">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'WhatsApp Submission Button Label', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[wa_btn_text]" value="<?php echo esc_attr( $stlcf_wa_txt ); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Email -->
                <div id="stlcf-tab-email" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Email Routing Settings', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Dual Submission (Email Button)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_email_btn" name="stlcf_general_settings[enable_email_btn]" value="1" <?php checked( $stlcf_e_btn, '1' ); ?>> <?php esc_html_e( 'Render secondary button choice enabling visitors to submit leads via email instead of WhatsApp.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-email-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Email Submission Button Label', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[email_btn_text]" value="<?php echo esc_attr( $stlcf_e_txt ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-email-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Email Button Color Accent', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="color" name="stlcf_general_settings[email_btn_color]" value="<?php echo esc_attr( $stlcf_e_clr ); ?>" class="stlcf-color-input">
                                </td>
                            </tr>
                            <tr class="stlcf-email-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Receiver Email Address Inbox', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="email" name="stlcf_general_settings[admin_email_receiver]" value="<?php echo esc_attr( $stlcf_a_rec ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Customer Auto-Responder', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_auto_responder" name="stlcf_general_settings[enable_auto_responder]" value="1" <?php checked( $stlcf_ar_en, '1' ); ?>> <?php esc_html_e( 'Automatically send a confirmation email back to the visitor if an email field is submitted.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-autoresponder-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Auto-Responder Subject Line', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[auto_responder_subject]" value="<?php echo esc_attr( $stlcf_ar_sub ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-autoresponder-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Auto-Responder Mail Body Message', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <textarea name="stlcf_general_settings[auto_responder_message]" rows="6" class="large-text"><?php echo esc_textarea( $stlcf_ar_msg ); ?></textarea>
                                    <p class="description"><?php esc_html_e( 'Format your message. Use tags [Your Name] and [Form Title] to dynamically personalize the customer auto-responder.', 'sanirtech-lead-chat-forms' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Smart Routing -->
                <div id="stlcf-tab-routing" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Smart Routing Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Dynamic Agent Dropdowns', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[enable_agent_routing]" value="1" <?php checked( $stlcf_agent_route, '1' ); ?>> <?php esc_html_e( 'Permit adding Custom Agent routing fields dropdown selectors inside the drag-and-drop form builders.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 5: Business Hours -->
                <div id="stlcf-tab-hours" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Business Operational Hours working Windows', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enforce Business Hours', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_business_hours" name="stlcf_general_settings[enable_business_hours]" value="1" <?php checked( $stlcf_hours_enabled, '1' ); ?>> <?php esc_html_e( 'Restrict WhatsApp redirections during custom business time windows.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Local Business Timezone Target', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[business_timezone]" value="<?php echo esc_attr( $stlcf_hours_tz ); ?>" placeholder="e.g. UTC, Asia/Kolkata, America/New_York" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Working Hours Window (Start - End)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="time" name="stlcf_general_settings[business_start]" value="<?php echo esc_attr( $stlcf_hours_start ); ?>">
                                    <span><?php esc_html_e( 'to', 'sanirtech-lead-chat-forms' ); ?></span>
                                    <input type="time" name="stlcf_general_settings[business_end]" value="<?php echo esc_attr( $stlcf_hours_end ); ?>">
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Weekly Working Days', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <div class="stlcf-days-checkbox-grid">
                                        <?php 
                                        $stlcf_days = array( 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun' );
                                        foreach ( $stlcf_days as $stlcf_key => $stlcf_lbl ) {
                                            $stlcf_checked = isset( $stlcf_business_days[$stlcf_key] ) && $stlcf_business_days[$stlcf_key] === '1' ? 'checked' : '';
                                            echo '<label class="stlcf-day-item"><input type="checkbox" name="stlcf_general_settings[business_days][' . esc_attr( $stlcf_key ) . ']" value="1" ' . esc_attr( $stlcf_checked ) . '> ' . esc_html( $stlcf_lbl ) . '</label>';
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Offline Redirect Behavior Action', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select id="stlcf_offline_action" name="stlcf_general_settings[offline_action]" style="min-width: 250px;">
                                        <option value="show_notice" <?php selected( $stlcf_offline_act, 'show_notice' ); ?>><?php esc_html_e( 'Show Offline Notice message (Disable Forms)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="email_only" <?php selected( $stlcf_offline_act, 'email_only' ); ?>><?php esc_html_e( 'Bypass WhatsApp, Route submissions via Email only', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="show_form" <?php selected( $stlcf_offline_act, 'show_form' ); ?>><?php esc_html_e( 'Bypass WhatsApp, load Custom Offline Form popup instead', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row id-stlcf-offline-form-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Select Offline Lead Form Popup', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select name="stlcf_general_settings[offline_form_id]" style="min-width: 250px;">
                                        <option value=""><?php esc_html_e( '-- Select Form --', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <?php
                                        global $wpdb;
                                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                                         $stlcf_all_forms = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}stlcf_forms WHERE status = 'active'" );
                                         // phpcs:enable
                                         if ( ! empty( $stlcf_all_forms ) ) {
                                             foreach ( $stlcf_all_forms as $stlcf_f ) {
                                                 $stlcf_selected = isset( $stlcf_options['offline_form_id'] ) && intval( $stlcf_options['offline_form_id'] ) === intval( $stlcf_f->id ) ? 'selected' : '';
                                                 echo sprintf( '<option value="%d" %s>%s</option>', intval( $stlcf_f->id ), esc_attr( $stlcf_selected ), esc_html( $stlcf_f->title ) );
                                             }
                                         }?>
                                    </select>
                                </td>
                            </tr>
                            <tr class="stlcf-hours-conditional-row id-stlcf-offline-msg-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Custom Offline Banner Message', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <textarea name="stlcf_general_settings[offline_message]" rows="3" class="large-text"><?php echo esc_textarea( $stlcf_offline_msg ); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 6: Floating Widget -->
                <div id="stlcf-tab-widget" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Floating Widget Preferences', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Site-Wide Floating Chat Button', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_floating_btn" name="stlcf_general_settings[floating_btn]" value="1" <?php checked( $stlcf_f_btn, '1' ); ?>> <?php esc_html_e( 'Render sticky floating WhatsApp launcher widget bottom corner of pages.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Button Color Accent', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="color" name="stlcf_general_settings[float_btn_color]" value="<?php echo esc_attr( $stlcf_fl_clr ); ?>" class="stlcf-color-input">
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Helper Tooltip Hover Text label', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[fw_tooltip_text]" value="<?php echo esc_attr( $stlcf_fw_tooltip ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Pre-filled WhatsApp Message text', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <textarea name="stlcf_general_settings[fw_prefilled_msg]" rows="3" class="large-text"><?php echo esc_textarea( $stlcf_fw_msg ); ?></textarea>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Widget Placement alignment', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select name="stlcf_general_settings[fw_position]">
                                        <option value="right" <?php selected( $stlcf_fw_pos, 'right' ); ?>><?php esc_html_e( 'Bottom Right Corner', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="left" <?php selected( $stlcf_fw_pos, 'left' ); ?>><?php esc_html_e( 'Bottom Left Corner', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Render Scope visibility rules', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select name="stlcf_general_settings[fw_visibility]">
                                        <option value="sitewide" <?php selected( $stlcf_fw_vis, 'sitewide' ); ?>><?php esc_html_e( 'Show Everywhere (Sitewide)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="homepage" <?php selected( $stlcf_fw_vis, 'homepage' ); ?>><?php esc_html_e( 'Home Page / Frontpage only', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Exit Intent Trigger (Desktop only)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[fw_exit_intent]" value="1" <?php checked( $stlcf_fw_exit, '1' ); ?>> <?php esc_html_e( 'Auto-display the widget when visitors mouse leaves viewport to exit page.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Time-Delay Trigger (Seconds)', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="number" name="stlcf_general_settings[fw_time_delay]" value="<?php echo esc_attr( $stlcf_fw_delay ); ?>" min="0" max="60" class="small-text">
                                    <span class="description"><?php esc_html_e( 'Seconds delay before launching floating chat widget on page load (0 to show instantly).', 'sanirtech-lead-chat-forms' ); ?></span>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Attention Wobble Animation', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php $stlcf_wobble_en = isset( $stlcf_options['fw_wobble_animation'] ) ? $stlcf_options['fw_wobble_animation'] : '0'; ?>
                                    <label><input type="checkbox" name="stlcf_general_settings[fw_wobble_animation]" value="1" <?php checked( $stlcf_wobble_en, '1' ); ?>> <?php esc_html_e( 'Periodic wobble attention animations to draw user eyes to the button.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Alert Notification Badge', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php $stlcf_badge_en = isset( $stlcf_options['fw_notif_badge'] ) ? $stlcf_options['fw_notif_badge'] : '0'; ?>
                                    <label><input type="checkbox" name="stlcf_general_settings[fw_notif_badge]" value="1" <?php checked( $stlcf_badge_en, '1' ); ?>> <?php esc_html_e( 'Renders a tiny red circle alert badge overlay on top of the widget.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Enable Multi-Agent Dashboard', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php $stlcf_multi_en = isset( $stlcf_options['enable_multi_agent'] ) ? $stlcf_options['enable_multi_agent'] : '0'; ?>
                                    <label><input type="checkbox" id="stlcf_enable_multi_agent" name="stlcf_general_settings[enable_multi_agent]" value="1" <?php checked( $stlcf_multi_en, '1' ); ?>> <?php esc_html_e( 'Enable listing multiple customer care support departments in the widget panel.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-multi-agent-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Support Agents list Registry', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <div id="stlcf-agents-repeater-container" style="max-width: 100%;">
                                        <?php 
                                        $stlcf_agents_list = isset( $stlcf_options['multi_agents_list'] ) ? maybe_unserialize( $stlcf_options['multi_agents_list'] ) : array();
                                        if ( is_array( $stlcf_agents_list ) ) {
                                            foreach ( $stlcf_agents_list as $stlcf_a_idx => $stlcf_agent ) {
                                                ?>
                                                <div class="stlcf-agent-repeater-row" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:12px; margin-bottom:10px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                                                    <input type="text" name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][name]" value="<?php echo esc_attr( $stlcf_agent['name'] ); ?>" placeholder="Agent Name" required style="flex:1; min-width:120px;">
                                                    <input type="text" name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][title]" value="<?php echo esc_attr( $stlcf_agent['title'] ); ?>" placeholder="Role / Department" style="flex:1; min-width:120px;">
                                                    <input type="text" name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][phone]" value="<?php echo esc_attr( $stlcf_agent['phone'] ); ?>" placeholder="Phone Number" required style="flex:1; min-width:150px;">
                                                    <select name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][status]" style="flex:1; min-width:100px;">
                                                        <option value="online" <?php selected( $stlcf_agent['status'], 'online' ); ?>><?php esc_html_e( 'Online', 'sanirtech-lead-chat-forms' ); ?></option>
                                                        <option value="away" <?php selected( $stlcf_agent['status'], 'away' ); ?>><?php esc_html_e( 'Away', 'sanirtech-lead-chat-forms' ); ?></option>
                                                        <option value="offline" <?php selected( $stlcf_agent['status'], 'offline' ); ?>><?php esc_html_e( 'Offline', 'sanirtech-lead-chat-forms' ); ?></option>
                                                    </select>
                                                    <input type="text" class="stlcf-agent-avatar-url" name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][avatar]" value="<?php echo esc_attr( $stlcf_agent['avatar'] ); ?>" placeholder="Avatar Image URL" style="flex:1.5; min-width:150px;">
                                                    <input type="text" name="stlcf_general_settings[multi_agents_list][<?php echo intval( $stlcf_a_idx ); ?>][allowed_countries]" value="<?php echo esc_attr( isset( $stlcf_agent['allowed_countries'] ) ? $stlcf_agent['allowed_countries'] : '' ); ?>" placeholder="Allowed Countries (e.g. US,CA)" style="flex:1; min-width:120px;">
                                                    <button type="button" class="button stlcf-remove-agent-btn" style="border-color:#dc2626; color:#dc2626;"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></button>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button type="button" id="stlcf-add-agent-btn" class="button button-secondary"><?php esc_html_e( '+ Add New Agent / Department Profile', 'sanirtech-lead-chat-forms' ); ?></button>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Widget FAQ Accordion Items', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <div id="stlcf-faq-repeater-container" style="max-width: 100%;">
                                        <?php 
                                        $stlcf_faq_list = isset( $stlcf_options['widget_faq_list'] ) ? maybe_unserialize( $stlcf_options['widget_faq_list'] ) : array();
                                        if ( is_array( $stlcf_faq_list ) ) {
                                            foreach ( $stlcf_faq_list as $stlcf_f_idx => $stlcf_faq ) {
                                                ?>
                                                <div class="stlcf-faq-repeater-row" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:12px; margin-bottom:10px; display:flex; flex-direction:column; gap:6px;">
                                                    <input type="text" name="stlcf_general_settings[widget_faq_list][<?php echo intval( $stlcf_f_idx ); ?>][question]" value="<?php echo esc_attr( $stlcf_faq['question'] ); ?>" placeholder="Question" required style="width:100%;">
                                                    <textarea name="stlcf_general_settings[widget_faq_list][<?php echo intval( $stlcf_f_idx ); ?>][answer]" placeholder="Answer Content" required style="width:100%;" rows="2"><?php echo esc_textarea( $stlcf_faq['answer'] ); ?></textarea>
                                                    <button type="button" class="button stlcf-remove-faq-btn" style="border-color:#dc2626; color:#dc2626; align-self:flex-end; margin-top:4px;"><?php esc_html_e( 'Delete FAQ', 'sanirtech-lead-chat-forms' ); ?></button>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button type="button" id="stlcf-add-faq-btn" class="button button-secondary"><?php esc_html_e( '+ Add New FAQ Accordion Item', 'sanirtech-lead-chat-forms' ); ?></button>
                                </td>
                            </tr>
                            <tr class="stlcf-widget-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Display Powered-by Link', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php $stlcf_powered_by = isset( $stlcf_options['enable_powered_by'] ) ? $stlcf_options['enable_powered_by'] : '1'; ?>
                                    <label><input type="checkbox" name="stlcf_general_settings[enable_powered_by]" value="1" <?php checked( $stlcf_powered_by, '1' ); ?>> <?php esc_html_e( 'Show a subtle referral branding link in the widget footer to support development (highly optional).', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 7: Analytics -->
                <div id="stlcf-tab-analytics" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Conversion Pixels & Analytics Tracking Integration', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Conversion Metrics Tracker', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_pixels_tracking" name="stlcf_general_settings[enable_pixels_tracking]" value="1" <?php checked( $stlcf_track_en, '1' ); ?>> <?php esc_html_e( 'Activate Facebook Pixel / GA4 conversion tracker hooks.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-analytics-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Meta Pixel ID', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[fb_pixel_id]" value="<?php echo esc_attr( $stlcf_fb_id ); ?>" placeholder="e.g. 1234567890" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-analytics-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Meta Pixel Event Name', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select name="stlcf_general_settings[fb_pixel_event]">
                                        <option value="Lead" <?php selected( $stlcf_pixel_event, 'Lead' ); ?>><?php esc_html_e( 'Lead (Default recommended)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="Contact" <?php selected( $stlcf_pixel_event, 'Contact' ); ?>><?php esc_html_e( 'Contact', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="SubmitApplication" <?php selected( $stlcf_pixel_event, 'SubmitApplication' ); ?>><?php esc_html_e( 'Submit Application', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="stlcf-analytics-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Google Analytics 4 Measurement ID', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[ga4_measurement_id]" value="<?php echo esc_attr( $stlcf_ga4_id ); ?>" placeholder="e.g. G-XXXXXX" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-analytics-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Inject Global Base Scripts', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[inject_base_scripts]" value="1" <?php checked( $stlcf_inject_base, '1' ); ?>> <?php esc_html_e( 'Check this option if your site does not have Google Tag Manager or Pixel scripts loaded already.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Local Analytics Dashboard', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" name="stlcf_general_settings[enable_analytics_dashboard]" value="1" <?php checked( $stlcf_dashboard_en, '1' ); ?>> <?php esc_html_e( 'Enforce rendering our visual graphs dashboard sub-menu tab inside admin menus.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 8: Spam Security -->
                <div id="stlcf-tab-security" class="stlcf-tab-section stlcf-card stlcf-hide-tab stlcf-security-box">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Anti-Spam Security Integrations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Select Captcha Provider', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <select id="stlcf_captcha_type" name="stlcf_general_settings[captcha_type]" class="stlcf-captcha-select">
                                        <option value="none" <?php selected( $stlcf_c_typ, 'none' ); ?>><?php esc_html_e( 'None (No validation challenges)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="built_in" <?php selected( $stlcf_c_typ, 'built_in' ); ?>><?php esc_html_e( 'Built-in Math Quiz (Low server footprint, no keys)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="turnstile" <?php selected( $stlcf_c_typ, 'turnstile' ); ?>><?php esc_html_e( 'Cloudflare Turnstile (Privacy-friendly challenge-free)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="recaptcha" <?php selected( $stlcf_c_typ, 'recaptcha' ); ?>><?php esc_html_e( 'Google reCAPTCHA v2 (Click Box check)', 'sanirtech-lead-chat-forms' ); ?></option>
                                        <option value="recaptcha_v3" <?php selected( $stlcf_c_typ, 'recaptcha_v3' ); ?>><?php esc_html_e( 'Google reCAPTCHA v3 (Invisible risk score checker)', 'sanirtech-lead-chat-forms' ); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <!-- Cloudflare Turnstile Settings -->
                            <tr class="stlcf-captcha-row stlcf-row-turnstile stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Turnstile Site Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[turnstile_site_key]" value="<?php echo esc_attr( $stlcf_ts_sk ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-captcha-row stlcf-row-turnstile stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Turnstile Secret Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="password" name="stlcf_general_settings[turnstile_secret_key]" value="<?php echo esc_attr( $stlcf_ts_sec ); ?>" class="regular-text">
                                </td>
                            </tr>

                            <!-- Google reCAPTCHA v2 Settings -->
                            <tr class="stlcf-captcha-row stlcf-row-recaptcha stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'reCAPTCHA v2 Site Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[recaptcha_site_key]" value="<?php echo esc_attr( $stlcf_r2_sk ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-captcha-row stlcf-row-recaptcha stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'reCAPTCHA v2 Secret Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="password" name="stlcf_general_settings[recaptcha_secret_key]" value="<?php echo esc_attr( $stlcf_r2_sec ); ?>" class="regular-text">
                                </td>
                            </tr>

                            <!-- Google reCAPTCHA v3 Settings -->
                            <tr class="stlcf-captcha-row stlcf-row-recaptcha-v3 stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'reCAPTCHA v3 Site Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[recaptcha_v3_site_key]" value="<?php echo esc_attr( $stlcf_r3_sk ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-captcha-row stlcf-row-recaptcha-v3 stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'reCAPTCHA v3 Secret Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="password" name="stlcf_general_settings[recaptcha_v3_secret_key]" value="<?php echo esc_attr( $stlcf_r3_sec ); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 9: Webhooks -->
                <div id="stlcf-tab-webhooks" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Zapier & Webhooks Dispatcher integration', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body" style="position: relative;">
                        <?php 
                        $stlcf_license = isset( $stlcf_options['pro_license_key'] ) ? trim( $stlcf_options['pro_license_key'] ) : '';
                        // Unlocked for free version release
                        $stlcf_is_pro = true;
                        if ( ! $stlcf_is_pro ) : 
                        ?>
                            <div class="stlcf-pro-lock-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 10; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; border-radius: 8px;">
                                <div style="background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 380px;">
                                    <span class="dashicons dashicons-lock" style="font-size: 40px; width: 40px; height: 40px; color: #b45309; margin-bottom: 10px; display: inline-block;"></span>
                                    <h4 style="margin: 0 0 10px 0; color: #1e293b; font-weight: 700; font-size: 15px;"><?php esc_html_e( 'Webhooks is a Pro Feature', 'sanirtech-lead-chat-forms' ); ?></h4>
                                    <p style="margin: 0 0 15px 0; font-size: 13px; color: #64748b; line-height: 1.4;">
                                        <?php esc_html_e( 'Instantly connect forms to thousands of cloud services like Zapier, Make, and webhooks by activating your Pro license key.', 'sanirtech-lead-chat-forms' ); ?>
                                    </p>
                                    <a href="#pro" onclick="jQuery('.stlcf-settings-sidebar-tab[href=\'#pro\']').trigger('click');" class="button button-primary" style="background: #b45309; border-color: #b45309; font-weight: 600;"><?php esc_html_e( 'Unlock Pro Version', 'sanirtech-lead-chat-forms' ); ?></a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Webhook Delivery', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_webhook" name="stlcf_general_settings[enable_webhook]" value="1" <?php checked( $stlcf_enable_webhook, '1' ); ?>> <?php esc_html_e( 'Asynchronously POST JSON payload variables to custom API servers (e.g. Zapier, Make).', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-webhook-conditional-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Target Webhook URL Endpoint', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="url" name="stlcf_general_settings[webhook_url]" value="<?php echo esc_url( $stlcf_webhook_url ); ?>" placeholder="https://hooks.zapier.com/hooks/catch/..." class="regular-text large-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 10: CRM Integrations -->
                <div id="stlcf-tab-integrations" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Native CRM Integration Settings', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body" style="position: relative;">
                        <?php if ( ! $stlcf_is_pro ) : ?>
                            <div class="stlcf-pro-lock-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 10; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; border-radius: 8px;">
                                <div style="background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 380px;">
                                    <span class="dashicons dashicons-lock" style="font-size: 40px; width: 40px; height: 40px; color: #b45309; margin-bottom: 10px; display: inline-block;"></span>
                                    <h4 style="margin: 0 0 10px 0; color: #1e293b; font-weight: 700; font-size: 15px;"><?php esc_html_e( 'CRM Syncing is a Pro Feature', 'sanirtech-lead-chat-forms' ); ?></h4>
                                    <p style="margin: 0 0 15px 0; font-size: 13px; color: #64748b; line-height: 1.4;">
                                        <?php esc_html_e( 'Sync captured leads automatically with HubSpot contacts and Mailchimp subscriber lists by activating your Pro license key.', 'sanirtech-lead-chat-forms' ); ?>
                                    </p>
                                    <a href="#pro" onclick="jQuery('.stlcf-settings-sidebar-tab[href=\'#pro\']').trigger('click');" class="button button-primary" style="background: #b45309; border-color: #b45309; font-weight: 600;"><?php esc_html_e( 'Unlock Pro Version', 'sanirtech-lead-chat-forms' ); ?></a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <table class="form-table">
                            <!-- Mailchimp -->
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Integrate Mailchimp marketing list', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_mailchimp" name="stlcf_general_settings[enable_mailchimp]" value="1" <?php checked( $stlcf_enable_mailchimp, '1' ); ?>> <?php esc_html_e( 'Add subscribers instantly to a Mailchimp Audience.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-mailchimp-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Mailchimp API Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="password" name="stlcf_general_settings[mailchimp_api_key]" value="<?php echo esc_attr( $stlcf_mailchimp_api ); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr class="stlcf-mailchimp-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'Audience / List ID Target', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <input type="text" name="stlcf_general_settings[mailchimp_list_id]" value="<?php echo esc_attr( $stlcf_mailchimp_list ); ?>" class="regular-text">
                                </td>
                            </tr>
                            
                            <!-- HubSpot -->
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Integrate HubSpot CRM', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <label><input type="checkbox" id="stlcf_enable_hubspot" name="stlcf_general_settings[enable_hubspot]" value="1" <?php checked( $stlcf_enable_hubspot, '1' ); ?>> <?php esc_html_e( 'Register leads as HubSpot Contacts.', 'sanirtech-lead-chat-forms' ); ?></label>
                                </td>
                            </tr>
                            <tr class="stlcf-hubspot-row stlcf-settings-conditional-row">
                                <th scope="row"><?php esc_html_e( 'HubSpot Private App Access Token', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <textarea name="stlcf_general_settings[hubspot_access_token]" rows="3" class="large-text"><?php echo esc_textarea( $stlcf_hubspot_token ); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tab 12: WooCommerce -->
                <div id="stlcf-tab-woocommerce" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'WooCommerce Product Integrations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
                            <div style="background: #f1f5f9; border-left: 4px solid #94a3b8; padding: 15px; border-radius: 6px; font-size: 13px; color: #475569; line-height: 1.5; text-align: left;">
                                <strong><?php esc_html_e( 'WooCommerce is Not Active', 'sanirtech-lead-chat-forms' ); ?></strong><br>
                                <?php esc_html_e( 'WooCommerce was not detected on this site. Please install and activate WooCommerce to enable single-product page quick inquiry buttons.', 'sanirtech-lead-chat-forms' ); ?>
                            </div>
                        <?php else : ?>
                            <div style="background: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; color: #166534; line-height: 1.5; text-align: left;">
                                <strong><?php esc_html_e( '🎉 WooCommerce Detected & Ready!', 'sanirtech-lead-chat-forms' ); ?></strong><br>
                                <?php esc_html_e( 'Enable the quick product inquiry feature below to display a conversion-focused "Inquire on WhatsApp" button directly on your single product pages.', 'sanirtech-lead-chat-forms' ); ?>
                            </div>

                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Enable Product WhatsApp Button', 'sanirtech-lead-chat-forms' ); ?></th>
                                    <td>
                                        <label><input type="checkbox" id="stlcf_enable_woo_button" name="stlcf_general_settings[enable_woo_button]" value="1" <?php checked( $stlcf_woo_enabled, '1' ); ?>> <?php esc_html_e( 'Display the WhatsApp button on single product pages next to the Add to Cart button.', 'sanirtech-lead-chat-forms' ); ?></label>
                                    </td>
                                </tr>
                                <tr class="stlcf-woo-conditional-row stlcf-settings-conditional-row">
                                    <th scope="row"><?php esc_html_e( 'WhatsApp Button Text', 'sanirtech-lead-chat-forms' ); ?></th>
                                    <td>
                                        <input type="text" name="stlcf_general_settings[woo_button_text]" value="<?php echo esc_attr( $stlcf_woo_btn_txt ); ?>" class="regular-text">
                                        <p class="description"><?php esc_html_e( 'Custom label displayed on the single product page action button.', 'sanirtech-lead-chat-forms' ); ?></p>
                                    </td>
                                </tr>
                                <tr class="stlcf-woo-conditional-row stlcf-settings-conditional-row">
                                    <th scope="row"><?php esc_html_e( 'Pre-filled WhatsApp Message', 'sanirtech-lead-chat-forms' ); ?></th>
                                    <td>
                                        <textarea name="stlcf_general_settings[woo_button_message]" rows="3" class="large-text"><?php echo esc_textarea( $stlcf_woo_btn_msg ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'The WhatsApp text message prefilled when a user clicks the button. Use the placeholders [Product Title] and [Product URL] to dynamically reference product details.', 'sanirtech-lead-chat-forms' ); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab 11: Go Pro -->
                <!-- Commented out for free version release
                <div id="stlcf-tab-pro" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
                    <div class="stlcf-card-header"><h2><?php esc_html_e( 'Activate Pro Version', 'sanirtech-lead-chat-forms' ); ?></h2></div>
                    <div class="stlcf-card-body">
                        <div style="background: #fff8e1; border-left: 4px solid #ffb300; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; color: #5d4037; line-height: 1.5; text-align: left;">
                            <strong><?php esc_html_e( '⭐ Unlock Advanced CRM Integrations & Fields!', 'sanirtech-lead-chat-forms' ); ?></strong><br>
                            <?php esc_html_e( 'Enter your license key below to unlock HubSpot sync, Mailchimp subscriber flows, conditional logic webhooks, file uploads, and A/B split-testing variant managers.', 'sanirtech-lead-chat-forms' ); ?>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Pro License Key', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td>
                                    <?php $stlcf_pro_key = isset( $stlcf_options['pro_license_key'] ) ? $stlcf_options['pro_license_key'] : ''; ?>
                                    <input type="password" name="stlcf_general_settings[pro_license_key]" value="<?php echo esc_attr( $stlcf_pro_key ); ?>" placeholder="e.g. SLFC-XXXX-XXXX-XXXX" class="regular-text">
                                    <p class="description">
                                        <?php 
                                        if ( ! empty( $stlcf_pro_key ) ) {
                                            echo '<span style="color: #10b981; font-weight: 600;">' . esc_html__( '✔ Pro License Active', 'sanirtech-lead-chat-forms' ) . '</span>';
                                        } else {
                                            echo wp_kses(
                                                sprintf(
                                                    /* translators: %s: Purchase URL */
                                                    esc_html__( 'Enter your license key to activate your Pro subscription. Don\'t have a key? <a href="%s" target="_blank">Get one here</a>.', 'sanirtech-lead-chat-forms' ),
                                                    'https://sanirtech.com/lead-chat-forms/buy/'
                                                ),
                                                array(
                                                    'a' => array(
                                                        'href'   => array(),
                                                        'target' => array(),
                                                    ),
                                                )
                                            );
                                        }
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                            <h3 style="font-size: 16px; margin-bottom: 15px; font-weight: 700; color: #1e293b; text-align: left;"><?php esc_html_e( 'Free vs. Pro Features Comparison', 'sanirtech-lead-chat-forms' ); ?></h3>
                            <table class="wp-list-table widefat fixed striped" style="max-width: 100%; border: 1px solid #cbd5e1; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background: #f8fafc;">
                                        <th style="padding: 12px; font-weight: 700; border-bottom: 2px solid #cbd5e1; text-align: left;"><?php esc_html_e( 'Feature', 'sanirtech-lead-chat-forms' ); ?></th>
                                        <th style="padding: 12px; font-weight: 700; text-align: center; border-bottom: 2px solid #cbd5e1; width: 100px;"><?php esc_html_e( 'Free', 'sanirtech-lead-chat-forms' ); ?></th>
                                        <th style="padding: 12px; font-weight: 700; text-align: center; border-bottom: 2px solid #cbd5e1; width: 100px; color: #b45309;"><?php esc_html_e( 'Pro', 'sanirtech-lead-chat-forms' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'Drag-and-Drop Form Builder', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'Local Leads Database Logger', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'Site-Wide Floating Chat Widget', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'HubSpot & Mailchimp Integrations', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #ef4444;">❌</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'A/B Split Testing variant manager', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #ef4444;">❌</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'Conditional Logic & Webhooks', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #ef4444;">❌</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px;"><strong><?php esc_html_e( 'Digital Signature Fields', 'sanirtech-lead-chat-forms' ); ?></strong></td>
                                        <td style="padding: 12px; text-align: center; color: #ef4444;">❌</td>
                                        <td style="padding: 12px; text-align: center; color: #10b981; font-weight: bold;">✔</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                -->

                <div style="margin-top: 20px;">
                    <?php submit_button( __( 'Save Dashboard Settings', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', true, array( 'style' => 'height: 42px; font-weight: 600;' ) ); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 1. Sidebar tab switching
    $('.stlcf-settings-sidebar-tab').on('click', function(e) {
        e.preventDefault();
        
        $('.stlcf-settings-sidebar-tab').removeClass('active');
        $(this).addClass('active');
        
        var targetSection = $(this).attr('data-tab');
        $('.stlcf-tab-section').hide();
        $('#' + targetSection).fadeIn(150);
        
        // Update URL hash without jumping
        var tabId = $(this).attr('href');
        history.pushState(null, null, tabId);
    });

    // Handle initial hash routing
    var hash = window.location.hash;
    if (hash) {
        var $targetTab = $('.stlcf-settings-sidebar-tab[href="' + hash + '"]');
        if ($targetTab.length > 0) {
            $targetTab.trigger('click');
        }
    }

    // 2. Conditional visibility animations
    function setupConditionalToggle(triggerSelector, targetSelector) {
        var $trigger = $(triggerSelector);
        function toggle() {
            if ($trigger.is(':checked')) {
                $(targetSelector).fadeIn(150);
            } else {
                $(targetSelector).hide();
            }
        }
        if ($trigger.length > 0) {
            $trigger.on('change', toggle);
            toggle(); // Run on init
        }
    }

    setupConditionalToggle('#stlcf_enable_gdpr', '.stlcf-gdpr-conditional-row');
    setupConditionalToggle('#stlcf_enable_gdpr_cron', '.stlcf-gdpr-cron-conditional-row');
    setupConditionalToggle('#stlcf_enable_email_btn', '.stlcf-email-conditional-row');
    setupConditionalToggle('#stlcf_enable_auto_responder', '.stlcf-autoresponder-conditional-row');
    setupConditionalToggle('#stlcf_enable_webhook', '.stlcf-webhook-conditional-row');
    setupConditionalToggle('#stlcf_enable_mailchimp', '.stlcf-mailchimp-row');
    setupConditionalToggle('#stlcf_enable_hubspot', '.stlcf-hubspot-row');
    setupConditionalToggle('#stlcf_enable_woo_button', '.stlcf-woo-conditional-row');

    // Business hours offline notice banner toggle
    var $hoursToggle = $('#stlcf_enable_business_hours');
    var $hoursAction = $('#stlcf_offline_action');
    function toggleHours() {
        if ($hoursToggle.is(':checked')) {
            $('.stlcf-hours-conditional-row').fadeIn(150);
            if ($hoursAction.val() === 'show_notice') {
                $('.id-stlcf-offline-msg-row').fadeIn(150);
                $('.id-stlcf-offline-form-row').hide();
            } else if ($hoursAction.val() === 'show_form') {
                $('.id-stlcf-offline-msg-row').hide();
                $('.id-stlcf-offline-form-row').fadeIn(150);
            } else {
                $('.id-stlcf-offline-msg-row').hide();
                $('.id-stlcf-offline-form-row').hide();
            }
        } else {
            $('.stlcf-hours-conditional-row').hide();
            $('.id-stlcf-offline-msg-row').hide();
            $('.id-stlcf-offline-form-row').hide();
        }
    }
    if ($hoursToggle.length > 0) {
        $hoursToggle.on('change', toggleHours);
        $hoursAction.on('change', toggleHours);
        toggleHours();
    }

    // Floating widget conditional logic
    var $widgetToggle = $('#stlcf_floating_btn');
    var $multiToggle = $('#stlcf_enable_multi_agent');
    function toggleWidget() {
        if ($widgetToggle.is(':checked')) {
            $('.stlcf-widget-conditional-row').fadeIn(150);
            if ($multiToggle.is(':checked')) {
                $('.stlcf-multi-agent-conditional-row').fadeIn(150);
            } else {
                $('.stlcf-multi-agent-conditional-row').hide();
            }
        } else {
            $('.stlcf-widget-conditional-row').hide();
            $('.stlcf-multi-agent-conditional-row').hide();
        }
    }
    if ($widgetToggle.length > 0) {
        $widgetToggle.on('change', toggleWidget);
        $multiToggle.on('change', toggleWidget);
        toggleWidget();
    }

    // Captcha dropdown dynamic conditional views
    var $captchaSelect = $('#stlcf_captcha_type');
    function toggleCaptcha() {
        var selectedVal = $captchaSelect.val();
        $('.stlcf-captcha-row').hide();
        if (selectedVal !== 'none' && selectedVal !== 'built_in') {
            var targetClass = selectedVal.replace('_', '-');
            $('.stlcf-row-' + targetClass).fadeIn(150);
        }
    }
    if ($captchaSelect.length > 0) {
        $captchaSelect.on('change', toggleCaptcha);
        toggleCaptcha();
    }

    // FAQ Repeater
    var faqRowIndex = $('#stlcf-faq-repeater-container .stlcf-faq-repeater-row').length;
    $('#stlcf-add-faq-btn').on('click', function(e) {
        e.preventDefault();
        var newFaqRow = '<div class="stlcf-faq-repeater-row" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:12px; margin-bottom:10px; display:flex; flex-direction:column; gap:6px;">' +
            '<input type="text" name="stlcf_general_settings[widget_faq_list][' + faqRowIndex + '][question]" placeholder="Question" required style="width:100%;">' +
            '<textarea name="stlcf_general_settings[widget_faq_list][' + faqRowIndex + '][answer]" placeholder="Answer Content" required style="width:100%;" rows="2"></textarea>' +
            '<button type="button" class="button stlcf-remove-faq-btn" style="border-color:#dc2626; color:#dc2626; align-self:flex-end; margin-top:4px;">Delete FAQ</button>' +
        '</div>';
        $('#stlcf-faq-repeater-container').append(newFaqRow);
        faqRowIndex++;
    });

    $(document).on('click', '.stlcf-remove-faq-btn', function(e) {
        e.preventDefault();
        $(this).closest('.stlcf-faq-repeater-row').remove();
    });
});
</script>