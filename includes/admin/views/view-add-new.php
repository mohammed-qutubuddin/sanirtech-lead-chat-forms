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
?>
<div class="wrap">
    <h1><?php echo $stlcf_is_editing_now ? esc_html__( 'Modify Form Attributes', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Create Custom WhatsApp Form', 'sanirtech-lead-chat-forms' ); ?></h1>
    
    <form method="POST" action="">
        <?php wp_nonce_field( 'stlcf_save_form_action', 'stlcf_form_nonce' ); ?>
        <input type="hidden" name="stlcf_action" value="save_form">
        <input type="hidden" name="form_id" value="<?php echo esc_attr( $stlcf_form_id ); ?>">

        <div class="stlcf-builder-layout">
            
            <div class="stlcf-builder-main stlcf-card">
                <div class="stlcf-card-body">
                    <div class="stlcf-form-group">
                        <label for="form_title"><?php esc_html_e( 'Form Name', 'sanirtech-lead-chat-forms' ); ?> <span class="stlcf-required">*</span></label>
                        <input type="text" id="form_title" name="form_title" value="<?php echo esc_attr( $stlcf_form_title ); ?>" placeholder="<?php esc_attr_e( 'e.g., Service Enquiry Form', 'sanirtech-lead-chat-forms' ); ?>" class="regular-text" required>
                    </div>
                    
                    <h3 class="stlcf-builder-heading"><?php esc_html_e( 'Form Fields (Drag & Drop to Reorder)', 'sanirtech-lead-chat-forms' ); ?></h3>
                    
                    <div id="stlcf-fields-container">
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
                                    </select>
                                    <input type="text" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][label]" value="<?php echo esc_attr( $stlcf_cur_lbl ); ?>" placeholder="<?php esc_attr_e( 'Field Label', 'sanirtech-lead-chat-forms' ); ?>" required>
                                    <label class="stlcf-req-checkbox-label">
                                        <input type="checkbox" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][required]" value="1" <?php checked( $stlcf_is_req, 1 ); ?>> 
                                        <?php esc_html_e( 'Required', 'sanirtech-lead-chat-forms' ); ?>
                                    </label>
                                    <button type="button" class="button remove-field-row"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></button>
                                </div>
                            <?php }
                        } ?>
                    </div>
                    
                    <button type="button" id="stlcf-add-field-btn" class="button button-secondary"><?php esc_html_e( '+ Add Custom Field Row (Repeater)', 'sanirtech-lead-chat-forms' ); ?></button>
                </div>
            </div>

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
                    <div class="stlcf-sidebar-actions">
                        <?php submit_button( $stlcf_is_editing_now ? esc_html__( 'Update Form Configurations', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Publish Form', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array( 'class' => 'button button-primary stlcf-btn-block' ) ); ?>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>