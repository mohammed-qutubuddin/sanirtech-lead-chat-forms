<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

$stlcf_form_id        = 0;
$stlcf_form_title     = '';
$stlcf_form_category  = 'general';
$stlcf_fields_array   = array( array( 'type' => 'text', 'label' => 'Your Name', 'required' => 1 ) ); 
$stlcf_is_editing_now = false;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'edit' && isset( $_GET['form_id'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_form_id = intval( wp_unslash( $_GET['form_id'] ) );
    
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
    $stlcf_form_record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_form_id ) );
    // phpcs:enable
    
    if ( $stlcf_form_record ) {
        $stlcf_is_editing_now = true;
        $stlcf_form_title     = $stlcf_form_record->title;
        $stlcf_form_category  = $stlcf_form_record->category;
        $stlcf_fields_array   = maybe_unserialize( $stlcf_form_record->fields );
    }
}

$stlcf_dynamic_categories = $this->get_all_categories();

// Fetch Global Smart Routing authorization state safely
$stlcf_settings_cache = get_option( 'stlcf_general_settings', array() );
$stlcf_agent_enabled  = isset( $stlcf_settings_cache['enable_agent_routing'] ) ? $stlcf_settings_cache['enable_agent_routing'] : '1';
?>
<div class="wrap">
    <h1><?php echo $stlcf_is_editing_now ? esc_html__( 'Modify Form Attributes', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Create Custom WhatsApp Form', 'sanirtech-lead-chat-forms' ); ?></h1>
    
    <form method="POST" action="">
        <?php wp_nonce_field( 'stlcf_save_form_action', 'stlcf_form_nonce' ); ?>
        <input type="hidden" name="stlcf_action" value="save_form">
        <input type="hidden" name="form_id" value="<?php echo esc_attr( $stlcf_form_id ); ?>">

        <div class="stlcf-builder-layout">
            
            <!-- Main Content Canvas (Left Column) -->
            <div class="stlcf-builder-main stlcf-card">
                <div class="stlcf-card-body">
                    <div class="stlcf-form-group">
                        <label for="form_title"><?php esc_html_e( 'Form Name', 'sanirtech-lead-chat-forms' ); ?> <span class="stlcf-required">*</span></label>
                        <input type="text" id="form_title" name="form_title" value="<?php echo esc_attr( $stlcf_form_title ); ?>" placeholder="<?php esc_attr_e( 'e.g., Service Enquiry Form', 'sanirtech-lead-chat-forms' ); ?>" class="regular-text" required>
                    </div>
                    
                    <!-- One-Click Preset Library Template Imports -->
                    <div style="margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <h4 style="margin: 0 0 10px 0; font-size: 13px; color: #475569; font-weight: 600;"><?php esc_html_e( 'Load Starter Preset Fields Template:', 'sanirtech-lead-chat-forms' ); ?></h4>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="button" class="button stlcf-preset-import-btn" data-preset="contact" style="background: #fff; border-color: #cbd5e1; font-weight: 600; font-size: 12px; color: #1e293b;">
                                <span class="dashicons dashicons-email" style="font-size: 14px; width: 14px; height: 14px; margin-top: 4px; margin-right: 4px;"></span>
                                <?php esc_html_e( 'Standard Contact Form', 'sanirtech-lead-chat-forms' ); ?>
                            </button>
                            <button type="button" class="button stlcf-preset-import-btn" data-preset="routing" style="background: #fff; border-color: #cbd5e1; font-weight: 600; font-size: 12px; color: #1e293b;">
                                <span class="dashicons dashicons-groups" style="font-size: 14px; width: 14px; height: 14px; margin-top: 4px; margin-right: 4px;"></span>
                                <?php esc_html_e( 'Agent Routing Form', 'sanirtech-lead-chat-forms' ); ?>
                            </button>
                            <button type="button" class="button stlcf-preset-import-btn" data-preset="upload" style="background: #fff; border-color: #cbd5e1; font-weight: 600; font-size: 12px; color: #1e293b;">
                                <span class="dashicons dashicons-cloud-upload" style="font-size: 14px; width: 14px; height: 14px; margin-top: 4px; margin-right: 4px;"></span>
                                <?php esc_html_e( 'File Submission Form', 'sanirtech-lead-chat-forms' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <h3 class="stlcf-builder-heading"><?php esc_html_e( 'Form Fields (Drag & Drop to Reorder)', 'sanirtech-lead-chat-forms' ); ?></h3>
                    
                    <!-- HTML5 data attribute configuration for JS state control mapping -->
                    <div id="stlcf-fields-container" data-agent-routing="<?php echo esc_attr( $stlcf_agent_enabled ); ?>">
                        <?php 
                        if ( is_array( $stlcf_fields_array ) ) {
                            foreach ( $stlcf_fields_array as $stlcf_idx => $stlcf_fld ) {
                                $stlcf_cur_type = isset( $stlcf_fld['type'] ) ? $stlcf_fld['type'] : 'text';
                                $stlcf_cur_lbl  = isset( $stlcf_fld['label'] ) ? $stlcf_fld['label'] : '';
                                $stlcf_is_req   = ( isset( $stlcf_fld['required'] ) && $stlcf_fld['required'] ) ? 1 : 0;
                                ?>
                                <div class="stlcf-field-row">
                                    <span class="dashicons dashicons-menu"></span>
                                    <select name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][type]">
                                         <option value="text" <?php selected( $stlcf_cur_type, 'text' ); ?>><?php esc_html_e( 'Text Field', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <option value="email" <?php selected( $stlcf_cur_type, 'email' ); ?>><?php esc_html_e( 'Email Field', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <option value="textarea" <?php selected( $stlcf_cur_type, 'textarea' ); ?>><?php esc_html_e( 'Textarea', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <option value="number" <?php selected( $stlcf_cur_type, 'number' ); ?>><?php esc_html_e( 'Number', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <option value="file" <?php selected( $stlcf_cur_type, 'file' ); ?>><?php esc_html_e( 'Secure File Upload', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <option value="signature" <?php selected( $stlcf_cur_type, 'signature' ); ?>><?php esc_html_e( 'Digital Signature Pad', 'sanirtech-lead-chat-forms' ); ?></option>
                                         
                                         <!-- CONDITION CONTROLLED OPTION NODE: Disappears when inactive globally, safe if data already exists -->
                                         <?php if ( $stlcf_agent_enabled === '1' || $stlcf_cur_type === 'agent_select' ) : ?>
                                             <option value="agent_select" <?php selected( $stlcf_cur_type, 'agent_select' ); ?>><?php esc_html_e( 'Agent Dropdown Routing', 'sanirtech-lead-chat-forms' ); ?></option>
                                         <?php endif; ?>
                                     </select>
                                    
                                    <input type="text" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][label]" value="<?php echo esc_attr( $stlcf_cur_lbl ); ?>" placeholder="<?php esc_attr_e( 'Field Label', 'sanirtech-lead-chat-forms' ); ?>" required>
                                    
                                    <label class="stlcf-req-checkbox-label">
                                        <input type="checkbox" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][required]" value="1" <?php checked( $stlcf_is_req, 1 ); ?>> 
                                        <?php esc_html_e( 'Required', 'sanirtech-lead-chat-forms' ); ?>
                                    </label>
                                    
                                    <button type="button" class="button remove-field-row"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></button>
                                    
                                    <!-- CONFIGURATION SUB-ROW: Repositioned to the bottom to sync accurately with structural DOM tree processing layouts -->
                                    <div class="stlcf-routing-config <?php echo ( $stlcf_cur_type !== 'agent_select' ) ? 'stlcf-hide-field' : ''; ?>">
                                        <p class="description"><?php esc_html_e( 'Define Agents Distribution configuration map (One entry per line):', 'sanirtech-lead-chat-forms' ); ?></p>
                                        <textarea name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][routing]" placeholder="<?php esc_attr_e( "Format: Name|Phone (With country code, no spaces)\nExample:\nSales Team|919999999999\nSupport Desk|918888888888", 'sanirtech-lead-chat-forms' ); ?>"><?php echo esc_textarea( isset( $stlcf_fld['routing'] ) ? $stlcf_fld['routing'] : '' ); ?></textarea>
                                    </div>

                                     <!-- CONDITIONAL LOGIC SUB-ROW -->
                                     <?php 
                                     $stlcf_cond_enabled  = isset( $stlcf_fld['cond_enabled'] ) ? intval( $stlcf_fld['cond_enabled'] ) : 0;
                                     $stlcf_cond_field    = isset( $stlcf_fld['cond_field'] ) ? $stlcf_fld['cond_field'] : '';
                                     $stlcf_cond_operator = isset( $stlcf_fld['cond_operator'] ) ? $stlcf_fld['cond_operator'] : 'equals';
                                     $stlcf_cond_value    = isset( $stlcf_fld['cond_value'] ) ? $stlcf_fld['cond_value'] : '';
                                     ?>
                                     <div class="stlcf-conditional-logic-trigger-wrapper" style="width:100%; margin-top:8px;">
                                         <label style="font-size:11px; font-weight:normal; display:inline-flex; align-items:center; gap:4px; user-select:none;">
                                             <input type="checkbox" class="stlcf-cond-toggle" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][cond_enabled]" value="1" <?php checked( $stlcf_cond_enabled, 1 ); ?>>
                                             <strong><?php esc_html_e( 'Enable Conditional Logic rules', 'sanirtech-lead-chat-forms' ); ?></strong>
                                         </label>
                                     </div>
                                     <div class="stlcf-conditional-logic-rules-panel <?php echo ( ! $stlcf_cond_enabled ) ? 'stlcf-hide-field' : ''; ?>" style="width:100%; margin-top:8px; padding:10px; background:#fff; border:1px solid #e2e8f0; border-radius:4px;">
                                         <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                             <span style="font-size:12px; font-weight:600;"><?php esc_html_e( 'Show this field if:', 'sanirtech-lead-chat-forms' ); ?></span>
                                             <select class="stlcf-cond-field-select" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][cond_field]" data-selected="<?php echo esc_attr( $stlcf_cond_field ); ?>" style="min-width:150px; font-size:12px; height:30px;">
                                                 <option value=""><?php esc_html_e( 'Select Target Field', 'sanirtech-lead-chat-forms' ); ?></option>
                                             </select>
                                             <select name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][cond_operator]" style="min-width:100px; font-size:12px; height:30px;">
                                                 <option value="equals" <?php selected( $stlcf_cond_operator, 'equals' ); ?>><?php esc_html_e( 'is equal to', 'sanirtech-lead-chat-forms' ); ?></option>
                                                 <option value="not_equals" <?php selected( $stlcf_cond_operator, 'not_equals' ); ?>><?php esc_html_e( 'is not equal to', 'sanirtech-lead-chat-forms' ); ?></option>
                                             </select>
                                             <input type="text" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][cond_value]" value="<?php echo esc_attr( $stlcf_cond_value ); ?>" placeholder="<?php esc_attr_e( 'Matching value...', 'sanirtech-lead-chat-forms' ); ?>" style="flex:1; min-width:120px; font-size:12px; height:30px; padding:0 8px;">
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                    
                    <button type="button" id="stlcf-add-field-btn" class="button button-secondary"><?php esc_html_e( '+ Add Custom Field Row (Repeater)', 'sanirtech-lead-chat-forms' ); ?></button>
                </div>
            </div>

            <!-- Contextual Settings Panels (Right Sidebar Column) -->
            <div class="stlcf-builder-sidebar stlcf-card">
                <div class="stlcf-card-body">
                    <div class="stlcf-form-group">
                        <label for="form_category"><?php esc_html_e( 'Form Category', 'sanirtech-lead-chat-forms' ); ?></label>
                        <select id="form_category" name="form_category">
                            <?php 
                            foreach ( $stlcf_dynamic_categories as $stlcf_slug => $stlcf_cat_data ) {
                                ?>
                                <option value="<?php echo esc_attr( $stlcf_slug ); ?>" <?php selected( $stlcf_form_category, $stlcf_slug ); ?>>
                                    <?php echo esc_html( $stlcf_cat_data['name'] ); ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <label for="form_layout"><?php esc_html_e( 'Form Layout Mode', 'sanirtech-lead-chat-forms' ); ?></label>
                        <?php $stlcf_form_layout = isset( $stlcf_form_record->layout ) ? $stlcf_form_record->layout : 'standard'; ?>
                        <select id="form_layout" name="form_layout">
                            <option value="standard" <?php selected( $stlcf_form_layout, 'standard' ); ?>><?php esc_html_e( 'Standard (All fields visible)', 'sanirtech-lead-chat-forms' ); ?></option>
                            <option value="conversational" <?php selected( $stlcf_form_layout, 'conversational' ); ?>><?php esc_html_e( 'Conversational (One field at a time)', 'sanirtech-lead-chat-forms' ); ?></option>
                        </select>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <?php $stlcf_rotator_val = isset( $stlcf_form_record->agent_rotator ) ? (int) $stlcf_form_record->agent_rotator : 0; ?>
                        <label style="font-weight: 600; font-size: 13px; color: #1e293b; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="agent_rotator" name="agent_rotator" value="1" <?php checked( $stlcf_rotator_val, 1 ); ?>>
                            <?php esc_html_e( 'Agent Round-Robin Rotator', 'sanirtech-lead-chat-forms' ); ?>
                        </label>
                        <p class="description" style="margin: 3px 0 0 20px; font-size: 11px; color: #64748b;">
                            <?php esc_html_e( 'If enabled, submissions are balanced round-robin among active online agents.', 'sanirtech-lead-chat-forms' ); ?>
                        </p>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 15px 0;">
                        <h4 style="margin: 0 0 10px 0; font-size: 13px; color: #0f172a; font-weight: 600;"><?php esc_html_e( 'Form Style Customizer', 'sanirtech-lead-chat-forms' ); ?></h4>
                        
                        <?php 
                        $stlcf_brand_color_val = isset( $stlcf_form_record->brand_color ) ? $stlcf_form_record->brand_color : ''; 
                        $stlcf_btn_txt_val = isset( $stlcf_form_record->button_text_color ) ? $stlcf_form_record->button_text_color : ''; 
                        $stlcf_radius_val = isset( $stlcf_form_record->border_radius ) ? $stlcf_form_record->border_radius : ''; 
                        ?>
                        
                        <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <label for="brand_color" style="font-size: 11px; font-weight: 600;"><?php esc_html_e( 'Primary Color', 'sanirtech-lead-chat-forms' ); ?></label>
                                <input type="color" id="brand_color" name="brand_color" value="<?php echo esc_attr( ! empty( $stlcf_brand_color_val ) ? $stlcf_brand_color_val : '#25d366' ); ?>" style="width: 100%; height: 32px; padding: 2px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer;">
                            </div>
                            <div style="flex: 1;">
                                <label for="button_text_color" style="font-size: 11px; font-weight: 600;"><?php esc_html_e( 'Button Text', 'sanirtech-lead-chat-forms' ); ?></label>
                                <input type="color" id="button_text_color" name="button_text_color" value="<?php echo esc_attr( ! empty( $stlcf_btn_txt_val ) ? $stlcf_btn_txt_val : '#ffffff' ); ?>" style="width: 100%; height: 32px; padding: 2px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer;">
                            </div>
                        </div>
                        <div>
                            <label for="border_radius" style="font-size: 11px; font-weight: 600;"><?php esc_html_e( 'Corner Radius (px)', 'sanirtech-lead-chat-forms' ); ?></label>
                            <input type="number" id="border_radius" name="border_radius" value="<?php echo esc_attr( ! empty( $stlcf_radius_val ) ? intval( $stlcf_radius_val ) : 8 ); ?>" min="0" max="50" style="width: 100%; font-size: 12px; height: 28px;">
                        </div>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <label for="ab_parent_id">
                            <?php esc_html_e( 'A/B Split Test Variant of', 'sanirtech-lead-chat-forms' ); ?>
                            <?php 
                            $stlcf_g_opts = get_option( 'stlcf_general_settings', array() );
                            // Unlocked for free version release
                            $stlcf_is_pro = true;
                            if ( ! $stlcf_is_pro ) : 
                            ?>
                                <span style="background: #fef3c7; color: #b45309; font-size: 9px; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-left: 6px; text-transform: uppercase;"><?php esc_html_e( 'Pro Locked', 'sanirtech-lead-chat-forms' ); ?></span>
                            <?php endif; ?>
                        </label>
                        <?php 
                        $stlcf_parent_val = isset( $stlcf_form_record->ab_parent_id ) ? intval( $stlcf_form_record->ab_parent_id ) : 0;
                        global $wpdb;
                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                        $stlcf_existing_forms = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}stlcf_forms WHERE ab_parent_id = 0 ORDER BY title ASC" );
                        // phpcs:enable
                        ?>
                        <select id="ab_parent_id" name="ab_parent_id" style="width: 100%;" <?php disabled( $stlcf_is_pro, false ); ?>>
                            <option value="0"><?php esc_html_e( '-- This is the Main Form (No Variant) --', 'sanirtech-lead-chat-forms' ); ?></option>
                            <?php 
                            if ( $stlcf_is_pro ) :
                                foreach ( $stlcf_existing_forms as $stlcf_f_item ) : 
                                    if ( isset( $stlcf_form_record->id ) && intval( $stlcf_form_record->id ) === intval( $stlcf_f_item->id ) ) { continue; }
                                    ?>
                                    <option value="<?php echo intval( $stlcf_f_item->id ); ?>" <?php selected( $stlcf_parent_val, intval( $stlcf_f_item->id ) ); ?>><?php echo esc_html( $stlcf_f_item->title ); ?></option>
                                <?php 
                                endforeach; 
                            endif;
                            ?>
                        </select>
                        <p class="description" style="margin-top: 5px; font-size: 11px; color: #64748b; line-height: 1.4;">
                            <strong><?php esc_html_e( 'Cache Notice:', 'sanirtech-lead-chat-forms' ); ?></strong>
                            <?php esc_html_e( 'Since A/B testing dynamically routes traffic on page loads, if your site utilizes aggressive page caching (e.g. WP Rocket, LiteSpeed Cache, Cloudflare), layouts could get locked. Please exclude URLs running split tests from your caching settings or use bypass rules.', 'sanirtech-lead-chat-forms' ); ?>
                        </p>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <label for="whatsapp_override"><?php esc_html_e( 'WhatsApp Number Override', 'sanirtech-lead-chat-forms' ); ?></label>
                        <?php $stlcf_whatsapp_override_val = isset( $stlcf_form_record->whatsapp_override ) ? $stlcf_form_record->whatsapp_override : ''; ?>
                        <input type="text" id="whatsapp_override" name="whatsapp_override" value="<?php echo esc_attr( $stlcf_whatsapp_override_val ); ?>" placeholder="<?php esc_attr_e( 'e.g. 919999999999 (default: global)', 'sanirtech-lead-chat-forms' ); ?>" style="width: 100%;">
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <label for="webhook_rules"><?php esc_html_e( 'Conditional Webhook Rules', 'sanirtech-lead-chat-forms' ); ?></label>
                        <?php $stlcf_webhook_rules_val = isset( $stlcf_form_record->webhook_rules ) ? $stlcf_form_record->webhook_rules : ''; ?>
                        <textarea id="webhook_rules" name="webhook_rules" rows="3" placeholder="<?php esc_attr_e( "Format: FieldLabel|Value|WebhookURL\nExample:\nDepartment|Sales|https://hooks.zapier.com/sales", 'sanirtech-lead-chat-forms' ); ?>" style="width: 100%; font-size: 11px;"><?php echo esc_textarea( $stlcf_webhook_rules_val ); ?></textarea>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <label for="email_rules"><?php esc_html_e( 'Conditional Email Notice Rules', 'sanirtech-lead-chat-forms' ); ?></label>
                        <?php $stlcf_email_rules_val = isset( $stlcf_form_record->email_rules ) ? $stlcf_form_record->email_rules : ''; ?>
                        <textarea id="email_rules" name="email_rules" rows="3" placeholder="<?php esc_attr_e( "Format: FieldLabel|Value|manager@email.com\nExample:\nDepartment|Support|support@company.com", 'sanirtech-lead-chat-forms' ); ?>" style="width: 100%; font-size: 11px;"><?php echo esc_textarea( $stlcf_email_rules_val ); ?></textarea>
                    </div>
                    <div class="stlcf-form-group" style="margin-top: 15px;">
                        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 15px 0;">
                        <?php 
                        $stlcf_ar_enabled = isset( $stlcf_form_record->autoresponder_enabled ) ? (int) $stlcf_form_record->autoresponder_enabled : 0; 
                        $stlcf_ar_subject = isset( $stlcf_form_record->autoresponder_subject ) ? $stlcf_form_record->autoresponder_subject : '';
                        $stlcf_ar_message = isset( $stlcf_form_record->autoresponder_message ) ? $stlcf_form_record->autoresponder_message : '';
                        ?>
                        <label style="font-weight: 600; font-size: 13px; color: #1e293b; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="autoresponder_enabled" name="autoresponder_enabled" value="1" <?php checked( $stlcf_ar_enabled, 1 ); ?> onclick="jQuery('.stlcf-ar-toggle-panel').toggle(this.checked);">
                            <?php esc_html_e( 'Custom Form Auto-Responder', 'sanirtech-lead-chat-forms' ); ?>
                        </label>
                        
                        <div class="stlcf-ar-toggle-panel" style="margin-top: 10px; display: <?php echo $stlcf_ar_enabled ? 'block' : 'none'; ?>; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; background: #f8fafc;">
                            <div class="stlcf-form-group" style="margin-bottom: 8px;">
                                <label for="autoresponder_subject" style="font-size: 11px; font-weight: 600;"><?php esc_html_e( 'Email Subject', 'sanirtech-lead-chat-forms' ); ?></label>
                                <input type="text" id="autoresponder_subject" name="autoresponder_subject" value="<?php echo esc_attr( $stlcf_ar_subject ); ?>" placeholder="<?php esc_attr_e( 'e.g. Thanks for your message!', 'sanirtech-lead-chat-forms' ); ?>" style="width:100%; font-size:12px;">
                            </div>
                            <div class="stlcf-form-group">
                                <label for="autoresponder_message" style="font-size: 11px; font-weight: 600;"><?php esc_html_e( 'Email Message Body', 'sanirtech-lead-chat-forms' ); ?></label>
                                <textarea id="autoresponder_message" name="autoresponder_message" rows="4" placeholder="<?php esc_attr_e( 'Use tag placeholders {your_name}, {email} to personalize body message text.', 'sanirtech-lead-chat-forms' ); ?>" style="width:100%; font-size:12px; height: 80px;"><?php echo esc_textarea( $stlcf_ar_message ); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="stlcf-sidebar-actions">
                        <?php submit_button( $stlcf_is_editing_now ? esc_html__( 'Update Form Configurations', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Publish Form', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array( 'class' => 'button button-primary stlcf-btn-block' ) ); ?>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>