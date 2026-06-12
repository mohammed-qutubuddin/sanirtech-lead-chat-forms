<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

$stlcf_options = get_option( 'stlcf_general_settings', array() );

// General & Styling Defaults
$stlcf_g_ph = isset( $stlcf_options['global_phone'] ) ? $stlcf_options['global_phone'] : '';
$stlcf_sv_db = isset( $stlcf_options['save_to_db'] ) ? $stlcf_options['save_to_db'] : '1';
$stlcf_btn_d = isset( $stlcf_options['button_design'] ) ? $stlcf_options['button_design'] : 'style_flat';
$stlcf_f_sz  = isset( $stlcf_options['font_size'] ) ? $stlcf_options['font_size'] : '16';
$stlcf_f_btn = isset( $stlcf_options['floating_btn'] ) ? $stlcf_options['floating_btn'] : '0';
$stlcf_wa_txt= isset( $stlcf_options['wa_btn_text'] ) ? $stlcf_options['wa_btn_text'] : 'Submit via WhatsApp';

// Email Settings Defaults
$stlcf_e_btn = isset( $stlcf_options['enable_email_btn'] ) ? $stlcf_options['enable_email_btn'] : '0';
$stlcf_e_txt = isset( $stlcf_options['email_btn_text'] ) ? $stlcf_options['email_btn_text'] : 'Submit via Email';
$stlcf_a_rec = isset( $stlcf_options['admin_email_receiver'] ) ? $stlcf_options['admin_email_receiver'] : get_option( 'admin_email' );

// Independent Color Options
$stlcf_b_clr = isset( $stlcf_options['btn_color'] ) ? $stlcf_options['btn_color'] : '#25D366'; // WhatsApp Form Button
$stlcf_e_clr = isset( $stlcf_options['email_btn_color'] ) ? $stlcf_options['email_btn_color'] : '#1e293b'; // Email Form Button
$stlcf_fl_clr = isset( $stlcf_options['float_btn_color'] ) ? $stlcf_options['float_btn_color'] : '#25D366'; // Floating Widget Button

// Captcha Defaults
$stlcf_c_typ = isset( $stlcf_options['captcha_type'] ) ? $stlcf_options['captcha_type'] : 'none';
$stlcf_ts_sk = isset( $stlcf_options['turnstile_site_key'] ) ? $stlcf_options['turnstile_site_key'] : '';
$stlcf_ts_sec= isset( $stlcf_options['turnstile_secret_key'] ) ? $stlcf_options['turnstile_secret_key'] : '';
$stlcf_r2_sk = isset( $stlcf_options['recaptcha_site_key'] ) ? $stlcf_options['recaptcha_site_key'] : '';
$stlcf_r2_sec= isset( $stlcf_options['recaptcha_secret_key'] ) ? $stlcf_options['recaptcha_secret_key'] : '';
$stlcf_r3_sk = isset( $stlcf_options['recaptcha_v3_site_key'] ) ? $stlcf_options['recaptcha_v3_site_key'] : '';
$stlcf_r3_sec= isset( $stlcf_options['recaptcha_v3_secret_key'] ) ? $stlcf_options['recaptcha_v3_secret_key'] : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Direct Chat & Lead Form - Settings', 'sanirtech-lead-chat-forms' ); ?></h1>
    <hr class="wp-header-end">
    
    <form method="post" action="options.php">
        <?php settings_fields( 'stlcf_settings_group' ); do_settings_sections( 'stlcf-settings-admin' ); ?>

        <!-- SECTION 1: General Options & Data -->
        <div class="postbox" style="margin-top:20px;">
            <div class="postbox-header"><h2 class="hndle" style="padding-left:15px;"><?php esc_html_e( 'General & Database Options', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside" style="padding:0 15px;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Global Target Number', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="text" name="stlcf_general_settings[global_phone]" value="<?php echo esc_attr( $stlcf_g_ph ); ?>" class="regular-text" placeholder="e.g. 919876543210">
                            <p class="description"><?php esc_html_e( 'Default phone number (with country code) for floating widgets and direct fallbacks.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Store Leads Locally', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[save_to_db]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[save_to_db]" value="1" <?php checked( $stlcf_sv_db, '1' ); ?>>
                                <?php esc_html_e( 'Save submitted form data entries inside the WordPress database.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECTION 2: Form Interface & Visual Styling -->
        <div class="postbox" style="margin-top:20px;">
            <div class="postbox-header"><h2 class="hndle" style="padding-left:15px;"><?php esc_html_e( 'Visual Settings & Styling', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside" style="padding:0 15px;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Sitewide Floating Button', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[floating_btn]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[floating_btn]" value="1" <?php checked( $stlcf_f_btn, '1' ); ?>>
                                <?php esc_html_e( 'Enable a sticky floating chat widget on all site pages.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Button Shape Design', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <fieldset>
                                <label style="margin-right: 20px;"><input type="radio" name="stlcf_general_settings[button_design]" value="style_flat" <?php checked( $stlcf_btn_d, 'style_flat' ); ?>><span style="background:#25D366; color:white; padding:4px 10px; border-radius:0px; font-weight:bold; font-size:12px;">Flat</span></label>
                                <label style="margin-right: 20px;"><input type="radio" name="stlcf_general_settings[button_design]" value="style_rounded" <?php checked( $stlcf_btn_d, 'style_rounded' ); ?>><span style="background:#25D366; color:white; padding:4px 10px; border-radius:4px; font-weight:bold; font-size:12px;">Rounded</span></label>
                                <label><input type="radio" name="stlcf_general_settings[button_design]" value="style_pill" <?php checked( $stlcf_btn_d, 'style_pill' ); ?>><span style="background:#25D366; color:white; padding:4px 15px; border-radius:20px; font-weight:bold; font-size:12px;">Pill</span></label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Form Font Size (px)', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="number" name="stlcf_general_settings[font_size]" value="<?php echo esc_attr( $stlcf_f_sz ); ?>" min="12" max="32" class="small-text"></td>
                    </tr>
                    
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <th scope="row"><label><?php esc_html_e( 'WhatsApp Button Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[wa_btn_text]" value="<?php echo esc_attr( $stlcf_wa_txt ); ?>" class="regular-text"></td>
                    </tr>
                    
                    <!-- Separate Color Panels -->
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'WhatsApp Button Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[btn_color]" value="<?php echo esc_attr( $stlcf_b_clr ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Email Button Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[email_btn_color]" value="<?php echo esc_attr( $stlcf_e_clr ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Floating Widget Color', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="color" name="stlcf_general_settings[float_btn_color]" value="<?php echo esc_attr( $stlcf_fl_clr ); ?>"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECTION 3: Secondary Email Action Configurations -->
        <div class="postbox" style="margin-top:20px;">
            <div class="postbox-header"><h2 class="hndle" style="padding-left:15px;"><?php esc_html_e( 'Email Submission Configurations', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside" style="padding:0 15px;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Email Submission Method', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <label>
                                <input type="hidden" name="stlcf_general_settings[enable_email_btn]" value="0">
                                <input type="checkbox" name="stlcf_general_settings[enable_email_btn]" value="1" <?php checked( $stlcf_e_btn, '1' ); ?>>
                                <?php esc_html_e( 'Offer a secondary button to submit the form data via site Email dispatch.', 'sanirtech-lead-chat-forms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Email Button Text', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td><input type="text" name="stlcf_general_settings[email_btn_text]" value="<?php echo esc_attr( $stlcf_e_txt ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Notification Receiver', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <input type="email" name="stlcf_general_settings[admin_email_receiver]" value="<?php echo esc_attr( $stlcf_a_rec ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Admin email address where the notification will be sent.', 'sanirtech-lead-chat-forms' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SECTION 4: Spam Protection Mechanisms -->
        <div class="postbox" style="margin-top:20px; border-left: 4px solid #b91c1c;">
            <div class="postbox-header"><h2 class="hndle" style="padding-left:15px;"><?php esc_html_e( 'Spam Security Rules', 'sanirtech-lead-chat-forms' ); ?></h2></div>
            <div class="inside" style="padding:0 15px;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Active Protection Layer', 'sanirtech-lead-chat-forms' ); ?></label></th>
                        <td>
                            <select id="stlcf_captcha_type" name="stlcf_general_settings[captcha_type]" style="width: 25em;">
                                <option value="none" <?php selected( $stlcf_c_typ, 'none' ); ?>><?php esc_html_e( 'Disabled (No Captcha)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="built_in" <?php selected( $stlcf_c_typ, 'built_in' ); ?>><?php esc_html_e( 'Built-in Mathematical Captcha', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="turnstile" <?php selected( $stlcf_c_typ, 'turnstile' ); ?>><?php esc_html_e( 'Cloudflare Turnstile Challenges', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="recaptcha" <?php selected( $stlcf_c_typ, 'recaptcha' ); ?>><?php esc_html_e( 'Google reCAPTCHA v2 (Checkboxes)', 'sanirtech-lead-chat-forms' ); ?></option>
                                <option value="recaptcha_v3" <?php selected( $stlcf_c_typ, 'recaptcha_v3' ); ?>><?php esc_html_e( 'Google reCAPTCHA v3 (Invisible/Score)', 'sanirtech-lead-chat-forms' ); ?></option>
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
            <?php submit_button( esc_html__( 'Save All Settings', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array('style'=>'font-size:15px; padding:6px 20px;') ); ?>
        </p>
    </form>
</div>