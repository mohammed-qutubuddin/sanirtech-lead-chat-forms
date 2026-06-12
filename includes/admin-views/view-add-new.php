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

        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="flex: 2; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="margin-bottom: 20px;">
                    <label for="form_title" style="display: block; font-weight: bold; margin-bottom: 8px;"><?php esc_html_e( 'Form Name', 'sanirtech-lead-chat-forms' ); ?> <span style="color:red;">*</span></label>
                    <input type="text" id="form_title" name="form_title" value="<?php echo esc_attr( $stlcf_form_title ); ?>" placeholder="e.g., Service Enquiry Form" class="regular-text" style="width: 100%; height: 40px;" required>
                </div>
                <h3><?php esc_html_e( 'Form Fields (Drag & Drop to Reorder)', 'sanirtech-lead-chat-forms' ); ?></h3>
                <div id="stlcf-fields-container" style="margin-top: 15px;">
                    <?php 
                    if ( is_array( $stlcf_fields_array ) ) {
                        foreach ( $stlcf_fields_array as $stlcf_idx => $stlcf_fld ) {
                            $stlcf_cur_type = isset( $stlcf_fld['type'] ) ? $stlcf_fld['type'] : 'text';
                            $stlcf_cur_lbl = isset( $stlcf_fld['label'] ) ? $stlcf_fld['label'] : '';
                            $stlcf_is_req = ( isset( $stlcf_fld['required'] ) && $stlcf_fld['required'] ) ? 1 : 0;
                            ?>
                            <div class="stlcf-field-row" style="background: #f8fafc; padding: 15px; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 10px; cursor: move; display: flex; align-items: center; gap: 15px;">
                                <span class="dashicons dashicons-menu" style="color: #94a3b8;"></span>
                                <select name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][type]" style="height: 35px;">
                                    <option value="text" <?php selected( $stlcf_cur_type, 'text' ); ?>>Text Field</option>
                                    <option value="email" <?php selected( $stlcf_cur_type, 'email' ); ?>>Email Field</option>
                                    <option value="textarea" <?php selected( $stlcf_cur_type, 'textarea' ); ?>>Textarea</option>
                                    <option value="number" <?php selected( $stlcf_cur_type, 'number' ); ?>>Number</option>
                                </select>
                                <input type="text" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][label]" value="<?php echo esc_attr( $stlcf_cur_lbl ); ?>" placeholder="Field Label" style="flex: 1; height: 35px;" required>
                                <label>
                                    <input type="checkbox" name="stlcf_fields[<?php echo intval( $stlcf_idx ); ?>][required]" value="1" <?php checked( $stlcf_is_req, 1 ); ?>> 
                                    <?php esc_html_e( 'Required', 'sanirtech-lead-chat-forms' ); ?>
                                </label>
                                <button type="button" class="button remove-field-row" style="color:red; border-color:red;">Delete</button>
                            </div>
                        <?php }
                    } ?>
                </div>
                <button type="button" id="stlcf-add-field-btn" class="button button-secondary" style="margin-top: 10px;">+ Add Custom Field Row (Repeater)</button>
            </div>

            <div style="flex: 1; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: fit-content;">
                <div style="margin-bottom: 20px;">
                    <label for="form_category" style="display: block; font-weight: bold; margin-bottom: 8px;"><?php esc_html_e( 'Form Category', 'sanirtech-lead-chat-forms' ); ?></label>
                    <select id="form_category" name="form_category" style="width: 100%; height: 35px;">
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
                <div style="border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <?php submit_button( $stlcf_is_editing_now ? esc_html__( 'Update Form Configurations', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Publish Form', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false, array('style' => 'width:100%; height:40px; font-size:15px;') ); ?>
                </div>
            </div>
        </div>
    </form>
</div>