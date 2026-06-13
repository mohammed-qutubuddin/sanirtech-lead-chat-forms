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

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
if ( $stlcf_filter_id > 0 ) {
    // FIXED: Corrected the dynamic database global variable properties indicator arrow token prefix format strings
    $stlcf_entries = $wpdb->get_results( $wpdb->prepare( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id WHERE e.form_id = %d ORDER BY e.id DESC", $stlcf_filter_id ) );
} else {
    // FIXED: Corrected the dynamic database global variable properties indicator arrow token prefix format strings
    $stlcf_entries = $wpdb->get_results( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id ORDER BY e.id DESC" );
}
// phpcs:enable

$stlcf_csv_nonce = wp_create_nonce( 'stlcf_export_csv_action' );
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Form Submissions Log', 'sanirtech-lead-chat-forms' ); ?></h1>
    
    <div class="stlcf-actions-header-wrapper" style="margin: 15px 0; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
        <?php if ( isset( $_GET['form_id'] ) && ! empty( $_GET['form_id'] ) ) : ?>
            <a href="admin.php?page=stlcf-entries" class="button button-secondary"><?php esc_html_e( 'Clear Filter / View All Logs', 'sanirtech-lead-chat-forms' ); ?></a>
        <?php endif; ?>
        
        <a href="<?php echo esc_url( admin_url( 'admin.php?action=stlcf_export_leads_csv&form_id=' . $stlcf_filter_id . '&_wpnonce=' . $stlcf_csv_nonce ) ); ?>" class="button button-primary stlcf-export-csv-btn" style="background: #10b981; border-color: #10b981; font-weight: 600;">
            <span class="dashicons dashicons-download" style="margin-top: 4px; margin-right: 4px;"></span>
            <?php esc_html_e( 'Export Filtered Leads to CSV', 'sanirtech-lead-chat-forms' ); ?>
        </a>
    </div>
    
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
                            /* translators: %d: Form ID */
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
                                            echo '<div class="stlcf-data-item"><strong>' . esc_html( $stlcf_lbl ) . ':</strong> ' . esc_html( $stlcf_val ) . '</div>';
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
                            <td>
                                <?php $stlcf_nonce = wp_create_nonce( 'stlcf_delete_entry_' . $stlcf_entry->id ); ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-entries&action=delete&entry_id=' . $stlcf_entry->id . '&_wpnonce=' . $stlcf_nonce ) ); ?>" class="stlcf-action-delete" onclick="return confirm('<?php esc_attr_e('Are you sure you want to permanently delete this entry record?', 'sanirtech-lead-chat-forms'); ?>');">
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