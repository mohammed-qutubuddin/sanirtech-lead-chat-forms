<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

$stlcf_table_abandoned = esc_sql( $wpdb->prefix . 'stlcf_abandoned_leads' );
$stlcf_table_forms     = esc_sql( $wpdb->prefix . 'stlcf_forms' );

// Fetch all recorded abandoned entries
$stlcf_query = "SELECT a.*, f.title as form_title FROM {$stlcf_table_abandoned} a LEFT JOIN {$stlcf_table_forms} f ON a.form_id = f.id ORDER BY a.id DESC";
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$stlcf_abandoned_leads = $wpdb->get_results( $stlcf_query );
// phpcs:enable

?>
<div class="wrap stlcf-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-dismiss" style="font-size: 24px; width: 24px; height: 24px; color: #ef4444;"></span>
            <?php esc_html_e( 'Abandoned Leads Recovery Log', 'sanirtech-lead-chat-forms' ); ?>
        </h1>
    </div>

    <?php
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( isset( $_GET['status'] ) && sanitize_key( wp_unslash( $_GET['status'] ) ) === 'deleted' ) {
        echo '<div class="notice notice-success is-dismissible" style="border-left-color: #ef4444;"><p>' . esc_html__( 'Abandoned lead record permanently removed from logs database.', 'sanirtech-lead-chat-forms' ) . '</p></div>';
    }
    ?>

    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
        <?php esc_html_e( 'These are leads captured automatically in real-time as users started typing but closed the form without submitting it.', 'sanirtech-lead-chat-forms' ); ?>
    </p>

    <div class="stlcf-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <table class="wp-list-table widefat fixed striped table-view-list" style="border: none; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569; width: 60px;"><?php esc_html_e( 'ID', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569; width: 200px;"><?php esc_html_e( 'Form Source', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569;"><?php esc_html_e( 'Captured Field Values', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569; width: 150px;"><?php esc_html_e( 'Visitor IP', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569; width: 180px;"><?php esc_html_e( 'Last Activity', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" style="padding: 15px; font-weight: 600; color: #475569; width: 100px; text-align: right;"><?php esc_html_e( 'Actions', 'sanirtech-lead-chat-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $stlcf_abandoned_leads ) ) : ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8; font-style: italic;">
                            <?php esc_html_e( 'No abandoned leads recovered in the system yet.', 'sanirtech-lead-chat-forms' ); ?>
                        </td>
                    </tr>
                <?php else : 
                    foreach ( $stlcf_abandoned_leads as $stlcf_lead ) : 
                        $stlcf_fields = maybe_unserialize( $stlcf_lead->form_data );
                        /* translators: %d: Deleted form identifier ID */
                        $stlcf_display_title = ! empty( $stlcf_lead->form_title ) ? $stlcf_lead->form_title : sprintf( __( 'Deleted Form (ID: %d)', 'sanirtech-lead-chat-forms' ), $stlcf_lead->form_id );
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px; vertical-align: middle;"><code>#<?php echo esc_html( $stlcf_lead->id ); ?></code></td>
                            <td style="padding: 15px; vertical-align: middle;"><strong style="color: #0f172a;"><?php echo esc_html( $stlcf_display_title ); ?></strong></td>
                            <td style="padding: 15px; vertical-align: middle;">
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    <?php 
                                    if ( is_array( $stlcf_fields ) ) {
                                        foreach ( $stlcf_fields as $stlcf_label => $stlcf_val ) {
                                            if ( ! empty( $stlcf_val ) ) {
                                                echo '<span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #334155;"><strong>' . esc_html( $stlcf_label ) . ':</strong> ' . esc_html( $stlcf_val ) . '</span>';
                                            }
                                        }
                                    } else {
                                        echo '<span style="color: #94a3b8;">' . esc_html__( 'No data entered yet', 'sanirtech-lead-chat-forms' ) . '</span>';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td style="padding: 15px; vertical-align: middle; color: #64748b;"><code><?php echo esc_html( $stlcf_lead->ip_address ); ?></code></td>
                            <td style="padding: 15px; vertical-align: middle; color: #64748b;"><?php echo esc_html( $stlcf_lead->updated_at ); ?></td>
                            <td style="padding: 15px; vertical-align: middle; text-align: right;">
                                <?php $stlcf_nonce = wp_create_nonce( 'stlcf_delete_abandoned_' . $stlcf_lead->id ); ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-abandoned&action=delete_abandoned&entry_id=' . $stlcf_lead->id . '&_wpnonce=' . $stlcf_nonce ) ); ?>" 
                                   style="color: #ef4444; text-decoration: none; font-weight: 600; font-size: 13px;" 
                                   onclick="return confirm('<?php esc_attr_e('Are you sure you want to permanently delete this recovered lead record?', 'sanirtech-lead-chat-forms'); ?>');">
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
