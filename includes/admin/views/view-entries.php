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
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Entry deleted.', 'sanirtech-lead-chat-forms' ) . '</p></div>';
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
    $stlcf_entries = $wpdb->get_results( $wpdb->prepare( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id WHERE e.form_id = %d ORDER BY e.id DESC", $stlcf_filter_id ) );
} else {
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $stlcf_entries = $wpdb->get_results( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id ORDER BY e.id DESC" );
}
// phpcs:enable
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Form Submissions Log', 'sanirtech-lead-chat-forms' ); ?></h1>
    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
    <?php if ( isset( $_GET['form_id'] ) && ! empty( $_GET['form_id'] ) ) : ?>
        <a href="admin.php?page=stlcf-entries" class="page-title-action"><?php esc_html_e( 'Clear Filter / View All Logs', 'sanirtech-lead-chat-forms' ); ?></a>
    <?php endif; ?>
    <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top:15px;">
        <thead>
            <tr>
                <th scope="col" style="width:60px;"><?php esc_html_e( 'ID', 'sanirtech-lead-chat-forms' ); ?></th>
                <th scope="col" style="width:180px;"><?php esc_html_e( 'Source', 'sanirtech-lead-chat-forms' ); ?></th>
                <th scope="col"><?php esc_html_e( 'Data Details', 'sanirtech-lead-chat-forms' ); ?></th>
                <th scope="col"><?php esc_html_e( 'URL', 'sanirtech-lead-chat-forms' ); ?></th>
                <th scope="col" style="width:140px;"><?php esc_html_e( 'Date', 'sanirtech-lead-chat-forms' ); ?></th>
                <th scope="col" style="width:80px;"><?php esc_html_e( 'Actions', 'sanirtech-lead-chat-forms' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $stlcf_entries ) ) : ?>
                <tr><td colspan="6"><?php esc_html_e( 'No entries found.', 'sanirtech-lead-chat-forms' ); ?></td></tr>
            <?php else : 
                foreach ( $stlcf_entries as $stlcf_entry ) : 
                    $stlcf_fields = maybe_unserialize( $stlcf_entry->form_data );
                    ?>
                    <tr>
                        <td><code>#<?php echo esc_html( $stlcf_entry->id ); ?></code></td>
                        <td><strong><?php echo esc_html( $stlcf_entry->form_title ); ?></strong></td>
                        <td>
                            <?php 
                            if ( is_array( $stlcf_fields ) ) {
                                foreach ( $stlcf_fields as $stlcf_lbl => $stlcf_val ) {
                                    echo '<div><strong>' . esc_html( $stlcf_lbl ) . ':</strong> ' . esc_html( $stlcf_val ) . '</div>';
                                }
                            }
                            ?>
                        </td>
                        <td><a href="<?php echo esc_url( $stlcf_entry->page_url ); ?>" target="_blank">View Link</a></td>
                        <td><?php echo esc_html( $stlcf_entry->submitted_at ); ?></td>
                        <td>
                            <?php $stlcf_nonce = wp_create_nonce( 'stlcf_delete_entry_' . $stlcf_entry->id ); ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-entries&action=delete&entry_id=' . $stlcf_entry->id . '&_wpnonce=' . $stlcf_nonce ) ); ?>" style="color:red;" onclick="return confirm('Confirm delete?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; 
            endif; ?>
        </tbody>
    </table>
</div>