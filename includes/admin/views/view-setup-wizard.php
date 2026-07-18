<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

// Process Setup Wizard saves
if ( isset( $_POST['stlcf_wizard_action'] ) && sanitize_key( wp_unslash( $_POST['stlcf_wizard_action'] ) ) === 'save_wizard' ) {
    if ( isset( $_POST['stlcf_wizard_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_wizard_nonce'] ) ), 'stlcf_save_wizard_action' ) ) {
        
        $stlcf_global_phone = isset( $_POST['global_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['global_phone'] ) ) : '';
        $stlcf_btn_color    = isset( $_POST['btn_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['btn_color'] ) ) : '#25d366';
        $stlcf_enable_gdpr  = isset( $_POST['enable_gdpr'] ) ? '1' : '0';
        $stlcf_gdpr_text    = isset( $_POST['gdpr_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gdpr_text'] ) ) : 'I agree to share my information via WhatsApp.';
        
        // Save Settings
        $stlcf_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_settings['global_phone'] = $stlcf_global_phone;
        $stlcf_settings['btn_color'] = $stlcf_btn_color;
        $stlcf_settings['enable_gdpr'] = $stlcf_enable_gdpr;
        $stlcf_settings['gdpr_text'] = $stlcf_gdpr_text;
        update_option( 'stlcf_general_settings', $stlcf_settings );
        
        // Create first pre-configured Form Template if requested
        $stlcf_create_preset = isset( $_POST['create_preset_form'] ) ? sanitize_key( wp_unslash( $_POST['create_preset_form'] ) ) : '';
        if ( ! empty( $stlcf_create_preset ) ) {
            $stlcf_table_forms = esc_sql( $wpdb->prefix . 'stlcf_forms' );
            
            $stlcf_title = __( 'Quick Contact Form', 'sanirtech-lead-chat-forms' );
            $stlcf_fields = array(
                array( 'type' => 'text', 'label' => 'Your Name', 'required' => 1 ),
                array( 'type' => 'email', 'label' => 'Email Address', 'required' => 1 ),
                array( 'type' => 'textarea', 'label' => 'Brief Message', 'required' => 1 )
            );
            
            if ( $stlcf_create_preset === 'routing' ) {
                $stlcf_title = __( 'Sales & Support Routing Form', 'sanirtech-lead-chat-forms' );
                $stlcf_fields = array(
                    array( 'type' => 'text', 'label' => 'Full Name', 'required' => 1 ),
                    array( 'type' => 'agent_select', 'label' => 'Department Name', 'required' => 1, 'routing' => "Sales Team|919999999999\nSupport Desk|918888888888" ),
                    array( 'type' => 'textarea', 'label' => 'Explain Your Inquiry', 'required' => 1 )
                );
            } elseif ( $stlcf_create_preset === 'upload' ) {
                $stlcf_title = __( 'Document Submission Form', 'sanirtech-lead-chat-forms' );
                $stlcf_fields = array(
                    array( 'type' => 'text', 'label' => 'Full Name', 'required' => 1 ),
                    array( 'type' => 'file', 'label' => 'Upload CV / Document', 'required' => 1 ),
                    array( 'type' => 'textarea', 'label' => 'Cover Note', 'required' => 0 )
                );
            }
            
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->insert(
                $stlcf_table_forms,
                array(
                    'title'    => $stlcf_title,
                    'fields'   => maybe_serialize( $stlcf_fields ),
                    'category' => 'general',
                    'layout'   => 'standard',
                    'created_at'=> current_time( 'mysql' )
                ),
                array( '%s', '%s', '%s', '%s', '%s' )
            );
            // phpcs:enable
        }
        
        echo '<script>window.location.href="admin.php?page=stlcf-forms&status=success";</script>';
        exit;
    }
}

$stlcf_settings = get_option( 'stlcf_general_settings', array() );
$stlcf_global_phone = isset( $stlcf_settings['global_phone'] ) ? $stlcf_settings['global_phone'] : '';
$stlcf_btn_color    = isset( $stlcf_settings['btn_color'] ) ? $stlcf_settings['btn_color'] : '#25D366';
$stlcf_enable_gdpr  = isset( $stlcf_settings['enable_gdpr'] ) ? $stlcf_settings['enable_gdpr'] : '0';
$stlcf_gdpr_text    = isset( $stlcf_settings['gdpr_text'] ) ? $stlcf_settings['gdpr_text'] : 'I consent to having this website store my submitted information.';
?>

<div class="stlcf-wizard-overlay" style="margin: 40px auto; max-width: 650px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px 40px; text-align: center; color: white;">
        <span class="dashicons dashicons-format-chat" style="font-size: 48px; width: 48px; height: 48px; display: inline-block; margin-bottom: 10px;"></span>
        <h1 style="margin: 0; color: white; font-size: 26px; font-weight: 700;"><?php esc_html_e( 'Quick-Start Setup Wizard', 'sanirtech-lead-chat-forms' ); ?></h1>
        <p style="margin: 10px 0 0 0; color: #a7f3d0; font-size: 14px; font-weight: 500;"><?php esc_html_e( 'Configure your WhatsApp lead capture channels in less than a minute.', 'sanirtech-lead-chat-forms' ); ?></p>
    </div>

    <form method="POST" action="" style="padding: 40px;">
        <?php wp_nonce_field( 'stlcf_save_wizard_action', 'stlcf_wizard_nonce' ); ?>
        <input type="hidden" name="stlcf_wizard_action" value="save_wizard">

        <!-- Step 1: Core Configuration -->
        <div class="stlcf-wizard-step" id="wizard-step-1">
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">1</span>
                <?php esc_html_e( 'Default WhatsApp Receiver Settings', 'sanirtech-lead-chat-forms' ); ?>
            </h2>

            <div style="margin-bottom: 20px;">
                <label for="global_phone" style="display: block; font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 8px;"><?php esc_html_e( 'Default WhatsApp Phone Number', 'sanirtech-lead-chat-forms' ); ?> <span style="color: #ef4444;">*</span></label>
                <input type="text" id="global_phone" name="global_phone" value="<?php echo esc_attr( $stlcf_global_phone ); ?>" placeholder="e.g. 919999999999 (include country code, no + or spaces)" style="width: 100%; height: 40px; padding: 0 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px;" required>
                <p style="font-size: 12px; color: #64748b; margin-top: 5px;"><?php esc_html_e( 'Always specify target routing phone numbers using international formats without prefixes or blank spacers.', 'sanirtech-lead-chat-forms' ); ?></p>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="btn_color" style="display: block; font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 8px;"><?php esc_html_e( 'Brand Theme Color Accent', 'sanirtech-lead-chat-forms' ); ?></label>
                <input type="color" id="btn_color" name="btn_color" value="<?php echo esc_attr( $stlcf_btn_color ); ?>" style="width: 60px; height: 35px; border: none; border-radius: 4px; cursor: pointer; padding: 0;">
                <span style="font-size: 12px; color: #64748b; margin-left: 10px;"><?php esc_html_e( 'Select the primary button color matching your website style.', 'sanirtech-lead-chat-forms' ); ?></span>
            </div>
        </div>

        <!-- Step 2: Compliance & GDPR -->
        <div class="stlcf-wizard-step" id="wizard-step-2" style="margin-top: 30px;">
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">2</span>
                <?php esc_html_e( 'GDPR & Privacy Consents Settings', 'sanirtech-lead-chat-forms' ); ?>
            </h2>

            <div style="margin-bottom: 20px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 14px; color: #1e293b;">
                    <input type="checkbox" name="enable_gdpr" value="1" <?php checked( $stlcf_enable_gdpr, '1' ); ?> onclick="jQuery('#wizard-gdpr-text-wrapper').toggle(this.checked);">
                    <?php esc_html_e( 'Enforce GDPR compliance consent flags on forms widget layouts', 'sanirtech-lead-chat-forms' ); ?>
                </label>
            </div>

            <div id="wizard-gdpr-text-wrapper" style="margin-bottom: 20px; display: <?php echo $stlcf_enable_gdpr === '1' ? 'block' : 'none'; ?>;">
                <label for="gdpr_text" style="display: block; font-weight: 600; font-size: 13px; color: #475569; margin-bottom: 8px;"><?php esc_html_e( 'Consent Statement Text message', 'sanirtech-lead-chat-forms' ); ?></label>
                <textarea id="gdpr_text" name="gdpr_text" rows="3" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;"><?php echo esc_textarea( $stlcf_gdpr_text ); ?></textarea>
            </div>
        </div>

        <!-- Step 3: Starter Presets -->
        <div class="stlcf-wizard-step" id="wizard-step-3" style="margin-top: 30px;">
            <h2 style="font-size: 18px; color: #1e293b; border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <span style="background: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">3</span>
                <?php esc_html_e( 'Auto-Generate Starter Template Option', 'sanirtech-lead-chat-forms' ); ?>
            </h2>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
                <label style="display: flex; align-items: center; gap: 10px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" class="wizard-preset-option">
                    <input type="radio" name="create_preset_form" value="standard" checked>
                    <div>
                        <strong style="display: block; color: #1e293b; font-size: 14px;"><?php esc_html_e( 'Standard Contact Form Preset', 'sanirtech-lead-chat-forms' ); ?></strong>
                        <span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Includes Name, Email address, and brief Inquiry message textbox.', 'sanirtech-lead-chat-forms' ); ?></span>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" class="wizard-preset-option">
                    <input type="radio" name="create_preset_form" value="routing">
                    <div>
                        <strong style="display: block; color: #1e293b; font-size: 14px;"><?php esc_html_e( 'Conditional Multi-Agent Routing Form Preset', 'sanirtech-lead-chat-forms' ); ?></strong>
                        <span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Includes dynamic agent select dropdown menu to route customers directly to Sales or Support target numbers.', 'sanirtech-lead-chat-forms' ); ?></span>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 10px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" class="wizard-preset-option">
                    <input type="radio" name="create_preset_form" value="upload">
                    <div>
                        <strong style="display: block; color: #1e293b; font-size: 14px;"><?php esc_html_e( 'Secure File Upload Submission Form Preset', 'sanirtech-lead-chat-forms' ); ?></strong>
                        <span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Includes custom PDF / Documents attachment field alongside text inputs.', 'sanirtech-lead-chat-forms' ); ?></span>
                    </div>
                </label>
            </div>
        </div>

        <!-- PHP Version Callout Notice -->
        <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; border-radius: 6px; margin-top: 25px; display: flex; align-items: flex-start; gap: 12px; font-size: 13px; color: #1e293b; line-height: 1.5;">
            <span class="dashicons dashicons-performance" style="color: #10b981; font-size: 20px; width: 20px; height: 20px; margin-top: 2px;"></span>
            <div>
                <strong><?php esc_html_e( 'System Performance Recommendation:', 'sanirtech-lead-chat-forms' ); ?></strong>
                <?php 
                $stlcf_current_php = PHP_VERSION;
                printf(
                    /* translators: %s: Current PHP Version */
                    esc_html__( 'Your server is currently running PHP version %s. While the plugin maintains full backward compatibility down to PHP 7.4, upgrading to PHP 8.1+ or 8.2+ is highly recommended to achieve optimal script execution speeds, lower database memory footprints, and hardened security compliance.', 'sanirtech-lead-chat-forms' ),
                    esc_html( $stlcf_current_php )
                );
                ?>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
            <button type="submit" class="button button-primary" style="background: #10b981; border-color: #10b981; font-weight: 600; height: 45px; padding: 0 30px; font-size: 15px; border-radius: 6px; box-shadow: 0 4px 6px rgba(16,185,129,0.2);"><?php esc_html_e( 'Complete Setup & Run Dashboard', 'sanirtech-lead-chat-forms' ); ?></button>
        </div>
    </form>
</div>
