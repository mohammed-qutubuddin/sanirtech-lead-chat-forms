<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'delete' && isset( $_GET['entry_id'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_del_id = intval( wp_unslash( $_GET['entry_id'] ) );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_delete_entry_' . $stlcf_del_id ) ) {
        if ( current_user_can( 'manage_options' ) ) {
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->delete( $wpdb->prefix . 'stlcf_entries', array( 'id' => $stlcf_del_id ), array( '%d' ) );
            // phpcs:enable
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Lead submission entry record dropped from log registries.', 'sanirtech-lead-chat-forms' ) . '</p></div>';
        }
    }
}

$stlcf_filter_id = 0;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['form_id'] ) && ! empty( $_GET['form_id'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_filter_id = intval( wp_unslash( $_GET['form_id'] ) );
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$stlcf_start_date_val = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$stlcf_end_date_val = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

$stlcf_query = "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id WHERE 1=1";
$stlcf_params = array();

if ( $stlcf_filter_id > 0 ) {
    $stlcf_query .= " AND e.form_id = %d";
    $stlcf_params[] = $stlcf_filter_id;
}

if ( ! empty( $stlcf_start_date_val ) ) {
    $stlcf_query .= " AND e.submitted_at >= %s";
    $stlcf_params[] = $stlcf_start_date_val . ' 00:00:00';
}

if ( ! empty( $stlcf_end_date_val ) ) {
    $stlcf_query .= " AND e.submitted_at <= %s";
    $stlcf_params[] = $stlcf_end_date_val . ' 23:59:59';
}

$stlcf_query .= " ORDER BY e.id DESC";

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
if ( ! empty( $stlcf_params ) ) {
    $stlcf_entries = $wpdb->get_results( $wpdb->prepare( $stlcf_query, $stlcf_params ) );
} else {
    $stlcf_entries = $wpdb->get_results( $stlcf_query );
}
// phpcs:enable

$stlcf_csv_nonce = wp_create_nonce( 'stlcf_export_csv_action' );
?>
<div class="wrap stlcf-admin-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-database-export" style="font-size: 24px; width: 24px; height: 24px; color: #3b82f6;"></span>
            <?php esc_html_e( 'WhatsApp Leads Database Logger', 'sanirtech-lead-chat-forms' ); ?>
        </h1>
    </div>

    <form method="get" action="admin.php" style="margin: 15px 0; background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="page" value="stlcf-entries">
        
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-weight: 600; font-size: 12px; color: #475569;"><?php esc_html_e( 'Filter by Form', 'sanirtech-lead-chat-forms' ); ?></label>
            <select name="form_id" style="height: 35px; min-width: 150px; font-size: 13px;">
                <option value=""><?php esc_html_e( 'All Forms', 'sanirtech-lead-chat-forms' ); ?></option>
                <?php
                // Fetch all forms for dropdown
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $stlcf_all_forms = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}stlcf_forms" );
                // phpcs:enable
                foreach ( $stlcf_all_forms as $stlcf_f_item ) {
                    echo '<option value="' . esc_attr( $stlcf_f_item->id ) . '" ' . selected( $stlcf_filter_id, $stlcf_f_item->id, false ) . '>' . esc_html( $stlcf_f_item->title ) . '</option>';
                }
                ?>
            </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-weight: 600; font-size: 12px; color: #475569;"><?php esc_html_e( 'Start Date', 'sanirtech-lead-chat-forms' ); ?></label>
            <input type="date" name="start_date" value="<?php echo esc_attr( $stlcf_start_date_val ); ?>" style="height: 35px; font-size: 13px; padding: 0 8px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-weight: 600; font-size: 12px; color: #475569;"><?php esc_html_e( 'End Date', 'sanirtech-lead-chat-forms' ); ?></label>
            <input type="date" name="end_date" value="<?php echo esc_attr( $stlcf_end_date_val ); ?>" style="height: 35px; font-size: 13px; padding: 0 8px;">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="button button-secondary" style="height: 35px; font-weight: 600;"><?php esc_html_e( 'Apply Filter', 'sanirtech-lead-chat-forms' ); ?></button>
            <?php if ( ! empty( $stlcf_filter_id ) || ! empty( $stlcf_start_date_val ) || ! empty( $stlcf_end_date_val ) ) : ?>
                <a href="admin.php?page=stlcf-entries" class="button button-link" style="height: 35px; line-height: 35px; text-decoration: none;"><?php esc_html_e( 'Clear', 'sanirtech-lead-chat-forms' ); ?></a>
            <?php endif; ?>
            
            <a href="<?php echo esc_url( admin_url( 'admin.php?action=stlcf_export_leads_csv&form_id=' . $stlcf_filter_id . '&start_date=' . $stlcf_start_date_val . '&end_date=' . $stlcf_end_date_val . '&_wpnonce=' . $stlcf_csv_nonce ) ); ?>" class="button button-primary stlcf-export-csv-btn" style="background: #10b981; border-color: #10b981; height: 35px; display: inline-flex; align-items: center; justify-content: center; font-weight: 600;">
                <span class="dashicons dashicons-download" style="margin-right: 4px; height: auto; width: auto; font-size: 16px;"></span>
                <?php esc_html_e( 'Export to CSV', 'sanirtech-lead-chat-forms' ); ?>
            </a>
        </div>
    </form>
    
    <div class="stlcf-table-container stlcf-mt-md">
        <table class="wp-list-table widefat fixed striped table-view-list stlcf-entries-table">
            <thead>
                <tr>
                    <th scope="col" class="stlcf-col-entry-id"><?php esc_html_e( 'ID', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entry-source"><?php esc_html_e( 'Source Form', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entry-data"><?php esc_html_e( 'Submitted Data Details', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entry-url"><?php esc_html_e( 'Page URL', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entry-date"><?php esc_html_e( 'Submission Date', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entry-actions"><?php esc_html_e( 'Actions', 'sanirtech-lead-chat-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $stlcf_entries ) ) : ?>
                    <tr>
                        <td colspan="6" class="stlcf-no-entries">
                            <?php esc_html_e( 'No lead entries recorded yet inside the local database.', 'sanirtech-lead-chat-forms' ); ?>
                        </td>
                    </tr>
                <?php else : 
                    foreach ( $stlcf_entries as $stlcf_entry ) : 
                        $stlcf_fields = maybe_unserialize( $stlcf_entry->form_data );
                        $stlcf_display_title = ! empty( $stlcf_entry->form_title ) ? $stlcf_entry->form_title : sprintf( 
                            /* translators: %d: Deleted form identifier ID */
                            __( 'Deleted Form (ID: %d)', 'sanirtech-lead-chat-forms' ), 
                            $stlcf_entry->form_id 
                        );
                        ?>
                        <tr>
                            <td><code class="stlcf-code-id">#<?php echo esc_html( $stlcf_entry->id ); ?></code></td>
                            <td><strong class="stlcf-entry-title"><?php echo esc_html( $stlcf_display_title ); ?></strong></td>
                            <td>
                                <div class="stlcf-entry-data-list">
                                    <?php 
                                    if ( is_array( $stlcf_fields ) ) {
                                        foreach ( $stlcf_fields as $stlcf_lbl => $stlcf_val ) {
                                            if ( filter_var( $stlcf_val, FILTER_VALIDATE_URL ) && ( strpos( $stlcf_val, '.png' ) !== false || strpos( $stlcf_val, '.jpg' ) !== false || strpos( $stlcf_val, '.jpeg' ) !== false || strpos( $stlcf_val, '.gif' ) !== false ) ) {
                                                if ( strpos( $stlcf_val, 'signature_' ) !== false ) {
                                                    echo '<div class="stlcf-data-item"><strong>' . esc_html( $stlcf_lbl ) . ':</strong><br><img src="' . esc_url( $stlcf_val ) . '" style="max-height: 40px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f8fafc; margin-top: 4px;" alt="Signature"></div>';
                                                } else {
                                                    echo '<div class="stlcf-data-item"><strong>' . esc_html( $stlcf_lbl ) . ':</strong> <a href="' . esc_url( $stlcf_val ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View Uploaded Image', 'sanirtech-lead-chat-forms' ) . '</a></div>';
                                                }
                                            } elseif ( filter_var( $stlcf_val, FILTER_VALIDATE_URL ) ) {
                                                echo '<div class="stlcf-data-item"><strong>' . esc_html( $stlcf_lbl ) . ':</strong> <a href="' . esc_url( $stlcf_val ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Download Document', 'sanirtech-lead-chat-forms' ) . '</a></div>';
                                            } else {
                                                echo '<div class="stlcf-data-item"><strong>' . esc_html( $stlcf_lbl ) . ':</strong> ' . esc_html( $stlcf_val ) . '</div>';
                                            }
                                        }
                                    } else {
                                        echo '<span class="stlcf-fallback-text">' . esc_html__( 'No structured dataset records parsed.', 'sanirtech-lead-chat-forms' ) . '</span>';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if ( ! empty( $stlcf_entry->page_url ) ) : ?>
                                    <a href="<?php echo esc_url( $stlcf_entry->page_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-small stlcf-link-btn">
                                        <?php esc_html_e( 'View Link', 'sanirtech-lead-chat-forms' ); ?> <span class="dashicons dashicons-external"></span>
                                    </a>
                                <?php else : ?>
                                    <span class="stlcf-fallback-text">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="stlcf-entry-date-cell"><?php echo esc_html( $stlcf_entry->submitted_at ); ?></td>
                            <td style="display: flex; gap: 8px; align-items: center;">
                                <?php $stlcf_print_nonce = wp_create_nonce( 'stlcf_print_entry_' . $stlcf_entry->id ); ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?action=stlcf_print_receipt&entry_id=' . $stlcf_entry->id . '&_wpnonce=' . $stlcf_print_nonce ) ); ?>" target="_blank" class="button button-small" style="display: inline-flex; align-items: center; gap: 4px; font-weight: 600;">
                                    <span class="dashicons dashicons-printer" style="font-size: 14px; width: 14px; height: 14px; margin-top: 1px;"></span>
                                    <?php esc_html_e( 'Print', 'sanirtech-lead-chat-forms' ); ?>
                                </a>
                                <?php $stlcf_nonce = wp_create_nonce( 'stlcf_delete_entry_' . $stlcf_entry->id ); ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-entries&action=delete&entry_id=' . $stlcf_entry->id . '&_wpnonce=' . $stlcf_nonce ) ); ?>" class="stlcf-action-delete" onclick="return confirm('<?php esc_attr_e('Are you sure you want to permanently delete this entry record?', 'sanirtech-lead-chat-forms'); ?>');" style="color: #ef4444; font-weight: 600; text-decoration: none; font-size: 13px;">
                                    <?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; 
                endif; ?>
            </tbody>
        </table>
    </div>
</div>