<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

$stlcf_options = get_option( 'stlcf_general_settings', array() );

// General & Database Defaults
$stlcf_g_ph  = isset( $stlcf_options['global_phone'] ) ? $stlcf_options['global_phone'] : '';
$stlcf_sv_db = isset( $stlcf_options['save_to_db'] ) ? $stlcf_options['save_to_db'] : '1';

// Styling Defaults
$stlcf_btn_d  = isset( $stlcf_options['button_design'] ) ? $stlcf_options['button_design'] : 'style_flat';
$stlcf_f_sz   = isset( $stlcf_options['font_size'] ) ? $stlcf_options['font_size'] : '16';
$stlcf_f_btn  = isset( $stlcf_options['floating_btn'] ) ? $stlcf_options['floating_btn'] : '0';
$stlcf_wa_txt = isset( $stlcf_options['wa_btn_text'] ) ? $stlcf_options['wa_btn_text'] : 'Submit via WhatsApp';

// Email Settings Defaults
$stlcf_e_btn = isset( $stlcf_options['enable_email_btn'] ) ? $stlcf_options['enable_email_btn'] : '0';
$stlcf_e_txt = isset( $stlcf_options['email_btn_text'] ) ? $stlcf_options['email_btn_text'] : 'Submit via Email';
$stlcf_a_rec = isset( $stlcf_options['admin_email_receiver'] ) ? $stlcf_options['admin_email_receiver'] : get_option( 'admin_email' );

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
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'SanirTech Lead Chat Forms - Settings', 'sanirtech-lead-chat-forms' ); ?></h1>
    <hr class="wp-header-end">
    
    <nav class="nav-tab-wrapper stlcf-tab-wrapper">
        <a href="#general" class="nav-tab stlcf-nav-tab nav-tab-active" data-tab="stlcf-tab-general"><?php esc_html_e( 'General Settings', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#styling" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-styling"><?php esc_html_e( 'Styling & Design', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#email" class="nav-tab stlcf-nav-tab" data-tab="stlcf-tab-email"><?php esc_html_e( 'Email Routing', 'sanirtech-lead-chat-forms' ); ?></a>
        <a href="#security" class="nav-tab stlcf-nav-tab stlcf-nav-tab-security" data-tab="stlcf-tab-security"><?php esc_html_e( 'Spam Security', 'sanirtech-lead-chat-forms' ); ?></a>
    </nav>

    <form method="post" action="options.php">
        <?php settings_fields( 'stlcf_settings_group' ); ?>

        <div id="stlcf-tab-general" class="stlcf-tab-section stlcf-card">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'General Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="stlcf-card-body">
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
                </table>
            </div>
        </div>

        <div id="stlcf-tab-styling" class="stlcf-tab-section stlcf-card stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Interface Appearance Customizations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="stlcf-card-body">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Sitewide Floating Widget', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[floating_btn]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[floating_btn]" value="1" <?php checked( $stlcf_f_btn, '1' ); ?>>
                                <?php esc_html_e( 'Display a sticky quick-chat WhatsApp floating button on all public pages.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
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
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Fallback Email Submissions', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="stlcf-card-body">
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
                            <p class="description"><?php esc_html_e( 'Target email account where the plain-text lead summaries are structured and sent.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="stlcf-tab-security" class="stlcf-tab-section stlcf-card stlcf-security-box stlcf-hide-tab">
            <div class="stlcf-card-header"><h2><?php esc_html_e( 'Anti-Spam Gateway Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="stlcf-card-body">
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

        <p class="submit">
            <?php submit_button( esc_html__( 'Save All Settings', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array( 'class' => 'button button-primary stlcf-submit-btn' ) ); ?>
        </p>
    </form>
</div>