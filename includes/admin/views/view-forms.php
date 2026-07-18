<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
$stlcf_forms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}stlcf_forms ORDER BY id DESC" );

// PERFORMANCE OPTIMIZATION: Fetch all entry counts grouped by form_id in ONE single query instead of executing inside the loop.
$stlcf_entry_counts = array();
$stlcf_raw_counts = $wpdb->get_results( "SELECT form_id, COUNT(*) as count FROM {$wpdb->prefix}stlcf_entries GROUP BY form_id", ARRAY_A );
// phpcs:enable

if ( is_array( $stlcf_raw_counts ) ) {
    foreach ( $stlcf_raw_counts as $stlcf_row ) {
        $stlcf_entry_counts[ intval( $stlcf_row['form_id'] ) ] = intval( $stlcf_row['count'] );
    }
}

$stlcf_categories_map = $this->get_all_categories();

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['status'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_status = sanitize_key( wp_unslash( $_GET['status'] ) );
    if ( $stlcf_status === 'success' ) { 
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'New form created successfully!', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    } elseif ( $stlcf_status === 'updated' ) { 
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Form configuration updated successfully!', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    } elseif ( $stlcf_status === 'deleted' ) { 
        echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'Form and its associated lead logs permanently deleted.', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    } elseif ( $stlcf_status === 'status_toggled' ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Form activation status changed.', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    }
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Direct Chat Forms', 'sanirtech-lead-chat-forms' ); ?></h1>
    <a href="admin.php?page=stlcf-add-new" class="page-title-action"><?php esc_html_e( 'Add New Form', 'sanirtech-lead-chat-forms' ); ?></a>
    <hr class="wp-header-end">
    
    <div class="stlcf-table-container">
        <table class="wp-list-table widefat fixed striped table-view-list stlcf-forms-table">
            <thead>
                <tr>
                    <th scope="col" class="stlcf-col-id"><?php esc_html_e( 'Form ID', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-name"><?php esc_html_e( 'Form Name', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-shortcode" style="width: 220px;"><?php esc_html_e( 'Shortcode & QR', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-category"><?php esc_html_e( 'Category', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-status" style="width: 100px;"><?php esc_html_e( 'Status', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-entries" style="width: 80px;"><?php esc_html_e( 'Leads', 'sanirtech-lead-chat-forms' ); ?></th>
                    <th scope="col" class="stlcf-col-ab" style="text-align: center;"><?php esc_html_e( 'A/B Testing Performance', 'sanirtech-lead-chat-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $stlcf_forms ) ) : ?>
                    <tr>
                        <td colspan="6" class="stlcf-no-forms">
                            <?php esc_html_e( 'No forms found. Click "Add New Form" to get started.', 'sanirtech-lead-chat-forms' ); ?>
                        </td>
                    </tr>
                <?php else : 
                    foreach ( $stlcf_forms as $stlcf_form ) : 
                        // Read from the pre-compiled lookup array cache instead of database hits
                        $stlcf_entry_count = isset( $stlcf_entry_counts[ $stlcf_form->id ] ) ? $stlcf_entry_counts[ $stlcf_form->id ] : 0;

                        $stlcf_edit_url    = admin_url( 'admin.php?page=stlcf-add-new&action=edit&form_id=' . $stlcf_form->id );
                        $stlcf_del_nonce   = wp_create_nonce( 'stlcf_delete_form_' . $stlcf_form->id );
                        $stlcf_delete_url  = admin_url( 'admin.php?page=stlcf-forms&action=delete_form&form_id=' . $stlcf_form->id . '&_wpnonce=' . $stlcf_del_nonce );
                        $stlcf_cat_key     = ! empty( $stlcf_form->category ) ? $stlcf_form->category : 'general';
                        $stlcf_cat_name    = isset( $stlcf_categories_map[$stlcf_cat_key] ) ? $stlcf_categories_map[$stlcf_cat_key]['name'] : $stlcf_cat_key;
                        ?>
                        <tr>
                            <td>
                                <span class="stlcf-id-text">#<?php echo esc_html( $stlcf_form->id ); ?></span>
                            </td>
                            
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url( $stlcf_edit_url ); ?>" class="stlcf-form-title-link">
                                        <?php echo esc_html( $stlcf_form->title ); ?>
                                    </a>
                                </strong>
                                <?php 
                                $stlcf_parent_id = isset( $stlcf_form->ab_parent_id ) ? intval( $stlcf_form->ab_parent_id ) : 0;
                                if ( $stlcf_parent_id > 0 ) : ?>
                                    <span class="stlcf-ab-variant-badge" style="background:#ffedd5; color:#c2410c; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:700; margin-left:6px; text-transform:uppercase; vertical-align:middle; display:inline-block; line-height:1.2; border:1px solid #fed7aa;"><?php esc_html_e('Variant', 'sanirtech-lead-chat-forms'); ?></span>
                                <?php endif; ?>
                                <div class="row-actions stlcf-row-actions-align">
                                    <span class="edit"><a href="<?php echo esc_url( $stlcf_edit_url ); ?>"><?php esc_html_e( 'Edit Fields', 'sanirtech-lead-chat-forms' ); ?></a> | </span>
                                    <span class="trash"><a href="<?php echo esc_url( $stlcf_delete_url ); ?>" class="stlcf-trash-link" onclick="return confirm('<?php esc_attr_e('Are you sure you want to permanently delete this form?', 'sanirtech-lead-chat-forms'); ?>');"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                </div>
                            </td>
                            
                            <td>
                                <div class="stlcf-shortcode-wrapper" style="display:flex; align-items:center;">
                                    <code class="stlcf-shortcode-code">[stlcf_chat_form id='<?php echo esc_attr( $stlcf_form->id ); ?>']</code>
                                    <button type="button" class="stlcf-copy-btn stlcf-list-copy-btn" data-clipboard="[stlcf_chat_form id='<?php echo esc_attr( $stlcf_form->id ); ?>']" title="<?php esc_attr_e( 'Copy Shortcode', 'sanirtech-lead-chat-forms' ); ?>">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </button>
                                    <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode( home_url( '?stlcf_view_form=' . $stlcf_form->id ) ); ?>" target="_blank" class="stlcf-copy-btn" title="<?php esc_attr_e( 'Scan/Download QR Code', 'sanirtech-lead-chat-forms' ); ?>" style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:28px; border-radius:4px; border:1px solid #cbd5e1; background:#ffffff; cursor:pointer; color:#475569; text-decoration:none; margin-left:4px; transition:all 0.2s;">
                                        <span class="dashicons dashicons-qr"></span>
                                    </a>
                                </div>
                            </td>
                            
                            <td>
                                <span class="stlcf-cat-badge">
                                    <?php echo esc_html( $stlcf_cat_name ); ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php 
                                $stlcf_status_val = isset( $stlcf_form->status ) ? $stlcf_form->status : 'active';
                                $stlcf_toggle_nonce = wp_create_nonce( 'stlcf_toggle_status_' . $stlcf_form->id );
                                $stlcf_toggle_url = admin_url( 'admin.php?page=stlcf-forms&action=toggle_status&form_id=' . $stlcf_form->id . '&_wpnonce=' . $stlcf_toggle_nonce );
                                
                                if ( $stlcf_status_val === 'active' ) {
                                    echo '<a href="' . esc_url( $stlcf_toggle_url ) . '" class="stlcf-status-active-badge" style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-weight: 600; text-decoration: none; font-size: 11px;">' . esc_html__( 'Active', 'sanirtech-lead-chat-forms' ) . '</a>';
                                } else {
                                    echo '<a href="' . esc_url( $stlcf_toggle_url ) . '" class="stlcf-status-inactive-badge" style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-weight: 600; text-decoration: none; font-size: 11px;">' . esc_html__( 'Inactive', 'sanirtech-lead-chat-forms' ) . '</a>';
                                }
                                ?>
                            </td>
                            
                            <td class="stlcf-text-center">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-entries&form_id=' . $stlcf_form->id ) ); ?>" class="stlcf-entries-badge">
                                    <?php echo esc_html( $stlcf_entry_count ); ?>
                                </a>
                            </td>

                            <td style="text-align:center;">
                                 <?php 
                                 $stlcf_views = isset( $stlcf_form->views ) ? intval( $stlcf_form->views ) : 0;
                                 $stlcf_conversions = isset( $stlcf_form->conversions ) ? intval( $stlcf_form->conversions ) : 0;
                                 $stlcf_cr_rate = $stlcf_views > 0 ? round( ($stlcf_conversions / $stlcf_views) * 100, 1 ) : 0;
                                 ?>
                                 <span style="font-weight: 600; font-size:12px; color:#334155;">
                                     <?php echo esc_html( $stlcf_views ); ?> / <?php echo esc_html( $stlcf_conversions ); ?> 
                                     <span style="color:#25d366; font-size:11px; margin-left:4px;">(<?php echo esc_html( $stlcf_cr_rate ); ?>%)</span>
                                 </span>
                            </td>
                        </tr>
                    <?php endforeach; 
                endif; ?>
            </tbody>
        </table>
    </div>
</div>