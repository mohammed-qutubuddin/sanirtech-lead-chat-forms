<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

$stlcf_options = get_option( 'stlcf_general_settings', array() );

// General & Database Defaults
$stlcf_g_ph  = isset( $stlcf_options['global_phone'] ) ? $stlcf_options['global_phone'] : '';
$stlcf_sv_db = isset( $stlcf_options['save_to_db'] ) ? $stlcf_options['save_to_db'] : '1';
$stlcf_adv_track = isset( $stlcf_options['enable_advanced_tracking'] ) ? $stlcf_options['enable_advanced_tracking'] : '1';
$stlcf_seo_schema = isset( $stlcf_options['enable_seo_schema'] ) ? $stlcf_options['enable_seo_schema'] : '1';

// GDPR Compliance Option Defaults
$stlcf_gdpr_en   = isset( $stlcf_options['enable_gdpr'] ) ? $stlcf_options['enable_gdpr'] : '0';
$stlcf_gdpr_txt  = isset( $stlcf_options['gdpr_text'] ) ? $stlcf_options['gdpr_text'] : '';
$stlcf_gdpr_page = isset( $stlcf_options['gdpr_privacy_page'] ) ? $stlcf_options['gdpr_privacy_page'] : '';

// Styling Defaults
$stlcf_btn_d  = isset( $stlcf_options['button_design'] ) ? $stlcf_options['button_design'] : 'style_flat';
$stlcf_f_sz   = isset( $stlcf_options['font_size'] ) ? $stlcf_options['font_size'] : '16';
$stlcf_wa_txt = isset( $stlcf_options['wa_btn_text'] ) ? $stlcf_options['wa_btn_text'] : 'Submit via WhatsApp';

// Email Settings & NEW Auto-Responder Defaults
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

// Smart Routing & Multi-Agent State
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
$stlcf_fw_tooltip   = isset( $stlcf_options['fw_tooltip_text'] ) ? $stlcf_options['fw_tooltip_text'] : 'Chat with us!';
$stlcf_fw_pos       = isset( $stlcf_options['fw_position'] ) ? $stlcf_options['fw_position'] : 'right';
$stlcf_fw_msg       = isset( $stlcf_options['fw_prefilled_msg'] ) ? $stlcf_options['fw_prefilled_msg'] : '';
$stlcf_fw_vis       = isset( $stlcf_options['fw_visibility'] ) ? $stlcf_options['fw_visibility'] : 'sitewide';

if ( empty( $stlcf_hours_tz ) ) { $stlcf_hours_tz = 'UTC'; }
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'SanirTech Lead Chat Forms - Settings', 'sanirtech-lead-chat-forms' ); ?></h1>
    <hr class="wp-header-end">
    
    <!-- Premium Navigation Tabs Base -->
    <nav class="nav-tab-wrapper stlcf-tab-wrapper">
        <a href="#general" class="nav-tab stlcf-nav-tab nav-tab-active" data-tab="stlcf-tab-general"><?php esc_html_e( 'General Settings', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#styling" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-styling"><?php esc_html_e( 'Styling & Design', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#email" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-email"><?php esc_html_e( 'Email Routing', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#routing" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-routing"><?php esc_html_e( 'Smart Routing', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#hours" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-hours"><?php esc_html_e( 'Business Hours', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#widget" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-widget"><?php esc_html_e( 'Floating Widget', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#analytics" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-analytics"><?php esc_html_e( 'Analytics & Pixels', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#security" class="nav-tab stlcf-nav-tab stlcf-nav-tab-security" data-tab="stlcf-tab-security"><?php esc_html_e( 'Spam Security', 'sanirtech-lead-chat-forms' ); ?></a>
    </nav>

    <form method="post" action="options.php">
        <?php settings_fields( 'stlcf_settings_group' ); ?>

        <!-- TAB 1: GENERAL SETTINGS -->
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
                            wp_dropdown_pages( array(
                                'name'             => 'stlcf_general_settings[gdpr_privacy_page]',
                                'selected'         => $stlcf_gdpr_page,
                                'show_option_none' => __( '-- Select Page Core Directory --', 'sanirtech-lead-chat-forms' ),
                                'class'            => 'stlcf-captcha-select regular-text'
                            ) );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TAB 2: STYLING & DESIGN -->
        <div id="stlcf-tab-styling" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Interface Appearance Customizations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Button Shape Geometry', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <fieldset>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_flat" <?php checked( $stlcf_btn_design, 'style_flat' ); ?>><span class="stlcf-radio-badge"><?php esc_html_e( 'Sharp Flat', 'sanirtech-lead-chat-forms' ); ?></span></label>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_rounded" <?php checked( $stlcf_btn_design, 'style_rounded' ); ?>><span class="stlcf-radio-badge stlcf-radio-badge-rounded"><?php esc_html_e( 'Modern Rounded', 'sanirtech-lead-chat-forms' ); ?></span></label>
                                <label class="stlcf-radio-label"><input type="radio" name="stlcf_general_settings[button_design]" value="style_pill" <?php checked( $stlcf_btn_design, 'style_pill' ); ?>><span class="stlcf-radio-badge stlcf-radio-badge-pill"><?php esc_html_e( 'Capsule Pill', 'sanirtech-lead-chat-forms' ); ?></span></label>
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

        <!-- TAB 3: EMAIL ROUTING & NEW AUTO-RESPONDER SECTIONS -->
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
                    
                    <!-- NEW: AUTOMATED LEAD ACKNOWLEDGEMENT ROW TOGGLE BUTTON -->
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
                        <td>
                            <input type="text" name="stlcf_general_settings[auto_responder_subject]" value="<?php echo esc_attr( $stlcf_ar_sub ); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr class="stlcf-autoresponder-conditional-row">
                        <th scope="row"><label><?php esc_html_e( 'Dynamic Reply Box Template Message', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <textarea name="stlcf_general_settings[auto_responder_message]" class="large-text" rows="6"><?php echo esc_textarea( $stlcf_ar_msg ); ?></textarea>
                            <p class="description">
                                <?php esc_html_e( 'Craft the reply message block. Pro Tip tokens placeholders list:', 'sanirtech-lead-chat-forms' ); ?><br>
                                <code>[Your Name]</code> - <?php esc_html_e( 'Auto-extracts input string labeled "Your Name" or generic first text input index descriptor.', 'sanirtech-lead-chat-forms' ); ?><br>
                                <code>[Form Title]</code> - <?php esc_html_e( 'Displays active form title identifier header string dynamically.', 'sanirtech-lead-chat-forms' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TAB 4: SMART ROUTING HUB -->
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
            </div>
        </div>

        <!-- TAB 5: BUSINESS HOURS MATRIX -->
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
                </table>
            </div>
        </div>

        <!-- TAB 6: FLOATING WIDGET -->
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
                </table>
            </div>
        </div>

        <!-- TAB 7: ANALYTICS & PIXELS -->
        <div id="stlcf-tab-analytics" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Conversion Analytics & Pixel Integrations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside stlcf-card-body">
                <table class="form-table" role="presentation">
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
                </table>
            </div>
        </div>

        <!-- TAB 8: SPAM SECURITY -->
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
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <p class="submit">
            <?php submit_button( esc_html__( 'Save All Settings', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array( 'class' => 'button button-primary stlcf-submit-btn' ) ); ?>
        </p>
    </form>
</div>