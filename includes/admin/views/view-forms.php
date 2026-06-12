<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
$stlcf_forms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}stlcf_forms ORDER BY id DESC" );
// phpcs:enable

$stlcf_categories_map = $this->get_all_categories();

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['status'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_status = sanitize_key( wp_unslash( $_GET['status'] ) );
    if ( $stlcf_status === 'success' ) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'New form created successfully!', 'sanirtech-lead-chat-forms' ) . '</p></div>'; }
    elseif ( $stlcf_status === 'updated' ) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Form configuration updated successfully!', 'sanirtech-lead-chat-forms' ) . '</p></div>'; }
    elseif ( $stlcf_status === 'deleted' ) { echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'Form and its associated lead logs permanently deleted.', 'sanirtech-lead-chat-forms' ) . '</p></div>'; }
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Direct Chat Forms', 'sanirtech-lead-chat-forms' ); ?></h1>
    <a href="admin.php?page=stlcf-add-new" class="page-title-action"><?php esc_html_e( 'Add New Form', 'sanirtech-lead-chat-forms' ); ?></a>
    <hr class="wp-header-end">
    
    <!-- Premium Card Wrapper for Table -->
    <div style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-top: 15px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <table class="wp-list-table widefat fixed striped table-view-list" style="border: none; margin: 0; box-shadow: none;">
            <thead>
                <tr>
                    <th scope="col" style="font-weight: 600; width: 80px; padding: 15px;"><?php esc_html_e( 'Form ID', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="font-weight: 600; padding: 15px;"><?php esc_html_e( 'Form Name', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="font-weight: 600; width: 350px; padding: 15px;"><?php esc_html_e( 'Shortcode', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="font-weight: 600; width: 180px; padding: 15px;"><?php esc_html_e( 'Category', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="font-weight: 600; width: 120px; padding: 15px; text-align: center;"><?php esc_html_e( 'Entries', 'sanirtech-lead-chat-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $stlcf_forms ) ) : ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #64748b; font-size: 14px;"><?php esc_html_e( 'No forms found. Click "Add New Form" to get started.', 'sanirtech-lead-chat-forms' ); ?></td></tr>
                <?php else : 
                    foreach ( $stlcf_forms as $stlcf_form ) : 
                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
                        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
                        $stlcf_entry_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}stlcf_entries WHERE form_id = %d", $stlcf_form->id ) );
                        // phpcs:enable

                        $stlcf_edit_url = admin_url( 'admin.php?page=stlcf-add-new&action=edit&form_id=' . $stlcf_form->id );
                        $stlcf_del_nonce = wp_create_nonce( 'stlcf_delete_form_' . $stlcf_form->id );
                        $stlcf_delete_url = admin_url( 'admin.php?page=stlcf-forms&action=delete_form&form_id=' . $stlcf_form->id . '&_wpnonce=' . $stlcf_del_nonce );
                        $stlcf_cat_key = ! empty( $stlcf_form->category ) ? $stlcf_form->category : 'general';
                        $stlcf_cat_name = isset( $stlcf_categories_map[$stlcf_cat_key] ) ? $stlcf_categories_map[$stlcf_cat_key]['name'] : $stlcf_cat_key;
                        ?>
                        <tr>
                            <!-- Form ID -->
                            <td style="padding: 15px; vertical-align: middle;">
                                <span style="color: #64748b; font-weight: 500;">#<?php echo esc_html( $stlcf_form->id ); ?></span>
                            </td>
                            
                            <!-- Form Name & Actions -->
                            <td style="padding: 15px; vertical-align: middle;">
                                <strong>
                                    <a href="<?php echo esc_url( $stlcf_edit_url ); ?>" style="font-size:14px; color:#2271b1; text-decoration: none; font-weight: 600;">
                                        <?php echo esc_html( $stlcf_form->title ); ?>
                                    </a>
                                </strong>
                                <div class="row-actions" style="margin-top: 5px; font-size: 13px;">
                                    <span class="edit"><a href="<?php echo esc_url( $stlcf_edit_url ); ?>"><?php esc_html_e( 'Edit Fields', 'sanirtech-lead-chat-forms' ); ?></a> | </span>
                                    <span class="trash"><a href="<?php echo esc_url( $stlcf_delete_url ); ?>" style="color:#b91c1c;" onclick="return confirm('<?php esc_html_e('Are you sure you want to permanently delete this form?', 'sanirtech-lead-chat-forms'); ?>');"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                </div>
                            </td>
                            
                            <!-- Interactive Shortcode Copier -->
                            <td style="padding: 15px; vertical-align: middle;">
                                <div style="display: inline-flex; align-items: center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px;">
                                    <code style="background: transparent; border: none; padding: 0; font-size: 13px; color: #0f172a; margin-right: 8px;">[stlcf_chat_form id='<?php echo esc_attr( $stlcf_form->id ); ?>']</code>
                                    <button type="button" class="stlcf-copy-btn" data-clipboard="[stlcf_chat_form id='<?php echo esc_attr( $stlcf_form->id ); ?>']" style="background: #e2e8f0; border: none; border-radius: 4px; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" title="<?php esc_html_e( 'Copy Shortcode', 'sanirtech-lead-chat-forms' ); ?>">
                                        <span class="dashicons dashicons-admin-page" style="font-size: 16px; width: 16px; height: 16px; color: #475569;"></span>
                                    </button>
                                </div>
                            </td>
                            
                            <!-- Category Badge -->
                            <td style="padding: 15px; vertical-align: middle;">
                                <span style="display: inline-block; padding: 4px 10px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 12px; font-weight: 500;">
                                    <?php echo esc_html( $stlcf_cat_name ); ?>
                                </span>
                            </td>
                            
                            <!-- Entries Counter Badge -->
                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-entries&form_id=' . $stlcf_form->id ) ); ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; background: #e0f2fe; color: #0284c7; border-radius: 50%; font-weight: bold; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'">
                                    <?php echo esc_html( $stlcf_entry_count ); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; 
                endif; ?>
            </tbody>
        </table>
    </div>
</div>