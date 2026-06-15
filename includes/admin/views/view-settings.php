<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

$stlcf_options = get_option( 'stlcf_general_settings', array() );

// General & Database Defaults
$stlcf_g_ph  = isset( $stlcf_options['global_phone'] ) ? $stlcf_options['global_phone'] : '';
$stlcf_sv_db = isset( $stlcf_options['save_to_db'] ) ? $stlcf_options['save_to_db'] : '1';
$stlcf_adv_track = isset( $stlcf_options['enable_advanced_tracking'] ) ? $stlcf_options['enable_advanced_tracking'] : '1';
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

// Captcha Defaults (RESTORED FULLY)
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

// Advanced Floating Widget Defaults (RESTORED FULLY)
$stlcf_f_btn        = isset( $stlcf_options['floating_btn'] ) ? $stlcf_options['floating_btn'] : '0';
$stlcf_fw_tooltip   = isset( $stlcf_options['fw_tooltip_text'] ) ? $stlcf_options['fw_tooltip_text'] : 'Chat with us!';
$stlcf_fw_pos       = isset( $stlcf_options['fw_position'] ) ? $stlcf_options['fw_position'] : 'right';
$stlcf_fw_msg       = isset( $stlcf_options['fw_prefilled_msg'] ) ? $stlcf_options['fw_prefilled_msg'] : '';
$stlcf_fw_vis       = isset( $stlcf_options['fw_visibility'] ) ? $stlcf_options['fw_visibility'] : 'sitewide';
$stlcf_dashboard_en = isset( $stlcf_options['enable_analytics_dashboard'] ) ? $stlcf_options['enable_analytics_dashboard'] : '1';

$stlcf_gdpr_cron = isset( $stlcf_options['enable_gdpr_cron'] ) ? $stlcf_options['enable_gdpr_cron'] : '0';
$stlcf_gdpr_days = isset( $stlcf_options['gdpr_retention_days'] ) ? $stlcf_options['gdpr_retention_days'] : '30';

if ( empty( $stlcf_hours_tz ) ) { $stlcf_hours_tz = 'UTC'; }
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'SanirTech Lead Chat Forms - Settings', 'sanirtech-lead-chat-forms' ); ?></h1>
    <hr class="wp-header-end">
    
    <nav class="nav-tab-wrapper stlcf-tab-wrapper">
        <a href="#general" class="nav-tab stlcf-nav-tab nav-tab-active" data-tab="stlcf-tab-general"><?php esc_html_e( 'General Settings', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#styling" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-styling"><?php esc_html_e( 'Styling & Design', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#email" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-email"><?php esc_html_e( 'Email Routing', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#routing" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-routing"><?php esc_html_e( 'Smart Routing', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#hours" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-hours"><?php esc_html_e( 'Business Hours', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#widget" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-widget"><?php esc_html_e( 'Floating Widget', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#analytics" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-analytics"><?php esc_html_e( 'Analytics & Pixels', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#security" class="nav-tab stlcf-nav-tab stlcf-nav-tab-security" data-tab="stlcf-tab-security"><?php esc_html_e( 'Spam Security', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#webhooks" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-webhooks"><?php esc_html_e( 'Webhooks & Zapier', 'sanirtech-lead-chat-forms' ); ?></a>
    </nav>

    <form method="post" action="options.php">
        <?php settings_fields( 'stlcf_settings_group' ); ?>

        <div id="stlcf-tab-general" class="stlcf-tab-section stlcf-card">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'General Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Global Target Number', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="text" name="stlcf_general_settings[global_phone]" value="<?php echo esc_attr( $stlcf_g_ph ); ?>" class="regular-text" placeholder="e.g. 919876543210">
                            <p class="description"><?php esc_html_e( 'Default phone number (with country code, no spaces) for widgets and fallbacks.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Local Leads Storage', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[save_to_db]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[save_to_db]" value="1" <?php checked( $stlcf_sv_db, '1' ); ?>>
                                <?php esc_html_e( 'Save copies of submitted form leads locally inside the WordPress database.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Advanced Page Tracking', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_advanced_tracking]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_advanced_tracking]" value="1" <?php checked( $stlcf_adv_track, '1' ); ?>>
                                <?php esc_html_e( 'Track and append Page Title, Post ID, and marketing parameters (UTM Source/Medium/Campaign) to lead payloads.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Automated SEO Schema', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_seo_schema]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_seo_schema]" value="1" <?php checked( $stlcf_seo_schema, '1' ); ?>>
                                <strong><?php esc_html_e( 'Inject automated valid JSON-LD ContactPoint schema graphs contextually.', 'sanirtech-lead-chat-forms' ); ?></strong>
                            </label>
                        </td>
                    </tr>
                    <tr style="border-top: 1px solid #f1f5f9;">
                        <th scope="row"><label><?php esc_html_e( 'GDPR Privacy Consent', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_gdpr]" value="0">
                                <input type="checkbox" id="stlcf_enable_gdpr" name="stlcf_general_settings[enable_gdpr]" value="1" <?php checked( $stlcf_gdpr_en, '1' ); ?>>
                                <?php esc_html_e( 'Enforce an obligatory consent validation checkbox above submission buttons layout.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr style="border-top: 1px solid #f1f5f9;">
                        <th scope="row"><label><?php esc_html_e( 'Automated Data Retention (Cron)', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_gdpr_cron]" value="0">
                                <input type="checkbox" id="stlcf_enable_gdpr_cron" name="stlcf_general_settings[enable_gdpr_cron]" value="1" <?php checked( $stlcf_gdpr_cron, '1' ); ?>>
                                <strong><?php esc_html_e( 'Automatically delete old leads from the database to comply with GDPR data minimization laws.', 'sanirtech-lead-chat-forms' ); ?></strong>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-gdpr-cron-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Data Retention Period', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select name="stlcf_general_settings[gdpr_retention_days]" class="stlcf-captcha-select">
                                <option value="15" <?php selected( $stlcf_gdpr_days, '15' ); ?>><?php esc_html_e( 'Delete leads older than 15 Days', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="30" <?php selected( $stlcf_gdpr_days, '30' ); ?>><?php esc_html_e( 'Delete leads older than 30 Days (Standard)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="60" <?php selected( $stlcf_gdpr_days, '60' ); ?>><?php esc_html_e( 'Delete leads older than 60 Days', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="90" <?php selected( $stlcf_gdpr_days, '90' ); ?>><?php esc_html_e( 'Delete leads older than 90 Days', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'A background WordPress Cron Job will execute daily to permanently erase expired records.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                    <tr class="stlcf-gdpr-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Consent Notice Text Statement', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <textarea name="stlcf_general_settings[gdpr_text]" class="large-text" rows="3"><?php echo esc_textarea( $stlcf_gdpr_txt ); ?></textarea>
                        </td>
                    </tr>
                    <tr class="stlcf-gdpr-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Target Privacy Page Anchor', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <?php 
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            wp_dropdown_pages( array( 
                                'name'             => 'stlcf_general_settings[gdpr_privacy_page]', 
                                'selected'         => intval( $stlcf_gdpr_page ), 
                                'show_option_none' => esc_html__( '-- Select Page Core Directory --', 'sanirtech-lead-chat-forms' ), 
                                'class'            => 'stlcf-captcha-select regular-text' 
                            ) ); 
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-styling" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Interface Appearance Customizations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Button Shape Geometry', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <fieldset>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_flat" <?php checked( $stlcf_btn_d, 'style_flat' ); ?>><span class="stlcf-radio-badge"><?php esc_html_e( 'Sharp Flat', 'sanirtech-lead-chat-forms' ); ?></span></label>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_rounded" <?php checked( $stlcf_btn_d, 'style_rounded' ); ?>><span class="stlcf-radio-badge stlcf-radio-badge-rounded"><?php esc_html_e( 'Modern Rounded', 'sanirtech-lead-chat-forms' ); ?></span></label>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_pill" <?php checked( $stlcf_btn_d, 'style_pill' ); ?>><span class="stlcf-radio-badge stlcf-radio-badge-pill"><?php esc_html_e( 'Capsule Pill', 'sanirtech-lead-chat-forms' ); ?></span></label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Form Typography Size', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="number" name="stlcf_general_settings[font_size]" value="<?php echo esc_attr( $stlcf_f_sz ); ?>" min="12" max="32" class="small-text"> <span class="description">px</span>
                        </td>
                    </tr>
                    <tr class="stlcf-border-row">
                        <th scope="row"><label><?php esc_html_e( 'WhatsApp Action Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[wa_btn_text]" value="<?php echo esc_attr( $stlcf_wa_txt ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'WhatsApp Branding Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[btn_color]" value="<?php echo esc_attr( $stlcf_b_clr ); ?>" class="stlcf-color-input"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Secondary Email Button Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[email_btn_color]" value="<?php echo esc_attr( $stlcf_e_clr ); ?>" class="stlcf-color-input"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Floating Widget Asset Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[float_btn_color]" value="<?php echo esc_attr( $stlcf_fl_clr ); ?>" class="stlcf-color-input"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-email" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Fallback Email Submissions & Automated Responders', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Dual Email Capability', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_email_btn]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_email_btn]" value="1" <?php checked( $stlcf_e_btn, '1' ); ?>>
                                <?php esc_html_e( 'Activate a secondary option to let users submit entries directly via server mail.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Email Action Button Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[email_btn_text]" value="<?php echo esc_attr( $stlcf_e_txt ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Notification Lead Destination', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="email" name="stlcf_general_settings[admin_email_receiver]" value="<?php echo esc_attr( $stlcf_a_rec ); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr style="border-top: 1px solid #f1f5f9;">
                        <th scope="row"><label><?php esc_html_e( 'Instant Lead Auto-Responder', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_auto_responder]" value="0">
                                <input type="checkbox" id="stlcf_enable_auto_responder" name="stlcf_general_settings[enable_auto_responder]" value="1" <?php checked( $stlcf_ar_en, '1' ); ?>>
                                <?php esc_html_e( 'Globally dispatch a personalized acknowledgement mail blueprint instantly upon user email lead generation.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-autoresponder-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Auto-Response Subject Line', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[auto_responder_subject]" value="<?php echo esc_attr( $stlcf_ar_sub ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-autoresponder-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Dynamic Reply Box Template Message', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <textarea name="stlcf_general_settings[auto_responder_message]" class="large-text" rows="6"><?php echo esc_textarea( $stlcf_ar_msg ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Craft the reply message block. Pro Tip tokens placeholders list: [Your Name] and [Form Title]', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-routing" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Multi-Agent Smart Routing Settings', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Dynamic Agent Processing', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_agent_routing]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_agent_routing]" value="1" <?php checked( $stlcf_agent_route, '1' ); ?>>
                                <?php esc_html_e( 'Allow frontend forms to parse advanced multi-agent dropdown routing instructions.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <div class="stlcf-doc-notice-box">
                    <h4><?php esc_html_e( '💡 Notice: How to add departments and numbers?', 'sanirtech-lead-chat-forms' ); ?></h4>
                    <p><?php esc_html_e( 'This setting simply enables the routing feature globally. To actually configure your agents and target phone numbers, please navigate to the "Add New" form builder and insert an "Agent Dropdown Routing" field block.', 'sanirtech-lead-chat-forms' ); ?></p>
                </div>
            </div>
        </div>

        <div id="stlcf-tab-hours" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Business Operating Hours Matrix', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Operating Hours Control', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_business_hours]" value="0">
                                <input type="checkbox" id="stlcf_enable_business_hours" name="stlcf_general_settings[enable_business_hours]" value="1" <?php checked( $stlcf_hours_enabled, '1' ); ?>>
                                <?php esc_html_e( 'Restrict chat submissions and floating widgets visibility to selective business windows.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-hours-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Select Timezone Reference', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select name="stlcf_general_settings[business_timezone]" class="stlcf-captcha-select">
                                <?php echo wp_timezone_choice( $stlcf_hours_tz ); ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="stlcf-hours-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Active Business Days', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <fieldset class="stlcf-days-checkbox-grid">
                                <?php 
                                $stlcf_days_map = array( 'monday'=>'Mon', 'tuesday'=>'Tue', 'wednesday'=>'Wed', 'thursday'=>'Thu', 'friday'=>'Fri', 'saturday'=>'Sat', 'sunday'=>'Sun' );
                                foreach ( $stlcf_days_map as $stlcf_day_key => $stlcf_day_lbl ) {
                                    $stlcf_day_checked = isset( $stlcf_business_days[$stlcf_day_key] ) ? $stlcf_business_days[$stlcf_day_key] : '0';
                                    ?>
                                    <label class="stlcf-day-item">
                                        <input type="hidden" name="stlcf_general_settings[business_days][<?php echo esc_attr( $stlcf_day_key ); ?>]" value="0">
                                        <input type="checkbox" name="stlcf_general_settings[business_days][<?php echo esc_attr( $stlcf_day_key ); ?>]" value="1" <?php checked( $stlcf_day_checked, '1' ); ?>>
                                        <?php echo esc_html( $stlcf_day_lbl ); ?>
                                    </label>
                                <?php } ?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr class="stlcf-hours-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Daily Time Window Frame', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="time" name="stlcf_general_settings[business_start]" value="<?php echo esc_attr( $stlcf_hours_start ); ?>"> 
                            <span>to</span>
                            <input type="time" name="stlcf_general_settings[business_end]" value="<?php echo esc_attr( $stlcf_hours_end ); ?>">
                        </td>
                    </tr>
                    <tr class="stlcf-hours-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Offline Enforcement Action', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select id="stlcf_offline_action" name="stlcf_general_settings[offline_action]" class="stlcf-captcha-select">
                                <option value="show_notice" <?php selected( $stlcf_offline_act, 'show_notice' ); ?>><?php esc_html_e( 'Display Offline Notice Banner above Form', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="email_only" <?php selected( $stlcf_offline_act, 'email_only' ); ?>><?php esc_html_e( 'Deactivate WhatsApp (Force Email Submissions)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="hide_widget" <?php selected( $stlcf_offline_act, 'hide_widget' ); ?>><?php esc_html_e( 'Completely Hide Site Floating Widget', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="stlcf-hours-conditional-row id-stlcf-offline-msg-row">
                        <th scope="row"><label><?php esc_html_e( 'Offline Alert Banner Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[offline_message]" value="<?php echo esc_attr( $stlcf_offline_msg ); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-widget" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'WhatsApp Sticky Floating Bubble Engine', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Enable Floating Bubble', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[floating_btn]" value="0">
                                <input type="checkbox" id="stlcf_floating_btn" name="stlcf_general_settings[floating_btn]" value="1" <?php checked( $stlcf_f_btn, '1' ); ?>>
                                <?php esc_html_e( 'Activate sticky quick-click sitewide floating action bubble trigger.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-widget-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Bubble Display Position', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select name="stlcf_general_settings[fw_position]" class="stlcf-captcha-select">
                                <option value="right" <?php selected( $stlcf_fw_pos, 'right' ); ?>><?php esc_html_e( 'Bottom Right Corner (Standard)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="left" <?php selected( $stlcf_fw_pos, 'left' ); ?>><?php esc_html_e( 'Bottom Left Corner', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="stlcf-widget-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Pop-up Tooltip Greeting', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="text" name="stlcf_general_settings[fw_tooltip_text]" value="<?php echo esc_attr( $stlcf_fw_tooltip ); ?>" class="regular-text" placeholder="e.g., Chat with us!">
                        </td>
                    </tr>
                    <tr class="stlcf-widget-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Pre-filled WhatsApp Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <textarea name="stlcf_general_settings[fw_prefilled_msg]" class="large-text" rows="2" placeholder="<?php esc_attr_e( 'e.g., Hi! I am visiting your site...', 'sanirtech-lead-chat-forms' ); ?>"><?php echo esc_textarea( $stlcf_fw_msg ); ?></textarea>
                        </td>
                    </tr>
                    <tr class="stlcf-widget-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Visibility Target Rules', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select name="stlcf_general_settings[fw_visibility]" class="stlcf-captcha-select">
                                <option value="sitewide" <?php selected( $stlcf_fw_vis, 'sitewide' ); ?>><?php esc_html_e( 'Sitewide (Show Everywhere)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="homepage" <?php selected( $stlcf_fw_vis, 'homepage' ); ?>><?php esc_html_e( 'Homepage Only', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="singular" <?php selected( $stlcf_fw_vis, 'singular' ); ?>><?php esc_html_e( 'Singular Posts & Pages Only', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-analytics" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Conversion Analytics & Pixel Integrations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Enable Analytics Dashboard', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_analytics_dashboard]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_analytics_dashboard]" value="1" <?php checked( $stlcf_dashboard_en, '1' ); ?>>
                                <strong><?php esc_html_e( 'Show the interactive Analytics menu to track lead conversions and form performance.', 'sanirtech-lead-chat-forms' ); ?></strong>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Asynchronous Event Tracking', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_pixels_tracking]" value="0">
                                <input type="checkbox" id="stlcf_enable_pixels_tracking" name="stlcf_general_settings[enable_pixels_tracking]" value="1" <?php checked( $stlcf_track_en, '1' ); ?>>
                                <?php esc_html_e( 'Activate async conversion events dispatch hooks for Google Analytics 4 and Meta Pixels.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-analytics-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Meta (Facebook) Pixel ID', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[fb_pixel_id]" value="<?php echo esc_attr( $stlcf_fb_id ); ?>" class="regular-text" placeholder="e.g. 123456789012345"></td>
                    </tr>
                    <tr class="stlcf-analytics-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Meta Standard Tracking Event', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select name="stlcf_general_settings[fb_pixel_event]" class="stlcf-captcha-select">
                                <option value="Lead" <?php selected( $stlcf_pixel_event, 'Lead' ); ?>><?php esc_html_e( 'Lead (Default Standard Option)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="Contact" <?php selected( $stlcf_pixel_event, 'Contact' ); ?>><?php esc_html_e( 'Contact Event', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="stlcf-analytics-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Google Analytics 4 ID', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[ga4_measurement_id]" value="<?php echo esc_attr( $stlcf_ga4_id ); ?>" class="regular-text" placeholder="e.g. G-XXXXXXX"></td>
                    </tr>
                    <tr class="stlcf-analytics-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Script Code Injection Rules', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[inject_base_scripts]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[inject_base_scripts]" value="1" <?php checked( $stlcf_inject_base, '1' ); ?>>
                                <strong><?php esc_html_e( 'Inject missing baseline header global scripts code snippets automatically.', 'sanirtech-lead-chat-forms' ); ?></strong>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-security" class="stlcf-tab-section stlcf-card stlcf-security-box stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Anti-Spam Gateway Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Anti-Bot Security Standard', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select id="stlcf_captcha_type" name="stlcf_general_settings[captcha_type]" class="stlcf-captcha-select">
                                <option value="none" <?php selected( $stlcf_c_typ, 'none' ); ?>><?php esc_html_e( 'No Captcha Engines Enforced', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="built_in" <?php selected( $stlcf_c_typ, 'built_in' ); ?>><?php esc_html_e( 'Lightweight Native Mathematical Quiz', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="turnstile" <?php selected( $stlcf_c_typ, 'turnstile' ); ?>><?php esc_html_e( 'Cloudflare Turnstile Validation (Recommended)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="recaptcha" <?php selected( $stlcf_c_typ, 'recaptcha' ); ?>><?php esc_html_e( 'Google reCAPTCHA v2 (Explicit Checkbox)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="recaptcha_v3" <?php selected( $stlcf_c_typ, 'recaptcha_v3' ); ?>><?php esc_html_e( 'Google reCAPTCHA v3 (Invisible Tracking Profile)', 'sanirtech-lead-chat-forms' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-turnstile">
                        <th scope="row"><label><?php esc_html_e( 'Turnstile Site Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[turnstile_site_key]" value="<?php echo esc_attr( $stlcf_ts_sk ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-turnstile">
                        <th scope="row"><label><?php esc_html_e( 'Turnstile Secret Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[turnstile_secret_key]" value="<?php echo esc_attr( $stlcf_ts_sec ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-recaptcha">
                        <th scope="row"><label><?php esc_html_e( 'reCAPTCHA v2 Site Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[recaptcha_site_key]" value="<?php echo esc_attr( $stlcf_r2_sk ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-recaptcha">
                        <th scope="row"><label><?php esc_html_e( 'reCAPTCHA v2 Secret Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[recaptcha_secret_key]" value="<?php echo esc_attr( $stlcf_r2_sec ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-recaptcha-v3">
                        <th scope="row"><label><?php esc_html_e( 'reCAPTCHA v3 Site Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[recaptcha_v3_site_key]" value="<?php echo esc_attr( $stlcf_r3_sk ); ?>" class="regular-text"></td>
                    </tr>
                    <tr class="stlcf-captcha-row stlcf-row-recaptcha-v3">
                        <th scope="row"><label><?php esc_html_e( 'reCAPTCHA v3 Secret Key', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[recaptcha_v3_secret_key]" value="<?php echo esc_attr( $stlcf_r3_sec ); ?>" class="regular-text"></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php
        // Fetch Webhook Defaults
        $stlcf_wh_en  = isset( $stlcf_options['enable_webhook'] ) ? $stlcf_options['enable_webhook'] : '0';
        $stlcf_wh_url = isset( $stlcf_options['webhook_url'] ) ? $stlcf_options['webhook_url'] : '';
        ?>
        <div id="stlcf-tab-webhooks" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Webhooks & CRM Push Integration', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Enable Outbound Webhooks', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_webhook]" value="0">
                                <input type="checkbox" id="stlcf_enable_webhook" name="stlcf_general_settings[enable_webhook]" value="1" <?php checked( $stlcf_wh_en, '1' ); ?>>
                                <?php esc_html_e( 'Automatically push form submission payload data to a custom Webhook URL (Zapier, Make, Pabbly, etc.) in real-time.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr class="stlcf-webhook-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Destination Webhook URL', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="url" name="stlcf_general_settings[webhook_url]" value="<?php echo esc_url( $stlcf_wh_url ); ?>" class="large-text" placeholder="https://hooks.zapier.com/hooks/catch/...">
                            <p class="description"><?php esc_html_e( 'The JSON payload will be dispatched asynchronously via a POST request to prevent frontend delays.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                </table>
                <div class="stlcf-doc-notice-box stlcf-webhook-conditional-row">
                    <h4><?php esc_html_e( '⚡ Payload Structure', 'sanirtech-lead-chat-forms' ); ?></h4>
                    <p><?php esc_html_e( 'The system sends a structured JSON array containing the Form ID, Source URL, Context Data, and all sanitized User Input fields directly to your endpoint.', 'sanirtech-lead-chat-forms' ); ?></p>
                </div>
            </div>
        </div>

        <p class="submit">
            <?php submit_button( esc_html__( 'Save All Settings', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array( 'class' => 'button button-primary stlcf-submit-btn' ) ); ?>
        </p>
    </form>
</div>