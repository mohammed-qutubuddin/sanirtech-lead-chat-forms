<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$stlcf_categories = $this->get_all_categories();
$stlcf_edit_slug = '';
$stlcf_edit_name = '';
$stlcf_edit_state = false;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'edit_cat' && isset( $_GET['cat_slug'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_edit_slug = sanitize_title( wp_unslash( $_GET['cat_slug'] ) );
    if ( isset( $stlcf_categories[$stlcf_edit_slug] ) ) {
        $stlcf_edit_state = true;
        $stlcf_edit_name = $stlcf_categories[$stlcf_edit_slug]['name'];
    }
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['status'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_st_check = sanitize_key( wp_unslash( $_GET['status'] ) );
    if ( $stlcf_st_check === 'cat_saved' ) { 
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Category configurations stored successfully.', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    } elseif ( $stlcf_st_check === 'cat_deleted' ) { 
        echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'Category eliminated. Forms reassigned to Default fallback.', 'sanirtech-lead-chat-forms' ) . '</p></div>'; 
    }
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Form Categories', 'sanirtech-lead-chat-forms' ); ?></h1>
    <div style="display:flex; gap:30px; margin-top:20px;">
        <div style="flex:1; max-width:400px; background:#fff; padding:20px;">
            <h2><?php echo $stlcf_edit_state ? esc_html__( 'Edit Category', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Add Category', 'sanirtech-lead-chat-forms' ); ?></h2>
            <form method="POST" action="">
                <?php wp_nonce_field( 'stlcf_save_cat_action', 'stlcf_cat_nonce' ); ?>
                <input type="hidden" name="stlcf_cat_action" value="save_category">
                <?php if ( $stlcf_edit_state ) : ?>
                    <input type="hidden" name="old_slug" value="<?php echo esc_attr( $stlcf_edit_slug ); ?>">
                <?php endif; ?>
                <div style="margin-bottom:15px;">
                    <label><?php esc_html_e( 'Name', 'sanirtech-lead-chat-forms' ); ?></label>
                    <input type="text" name="cat_name" value="<?php echo esc_attr( $stlcf_edit_name ); ?>" style="width:100%;" required>
                </div>
                <div style="margin-bottom:20px;">
                    <label><?php esc_html_e( 'Slug', 'sanirtech-lead-chat-forms' ); ?></label>
                    <input type="text" name="cat_slug" value="<?php echo esc_attr( $stlcf_edit_slug ); ?>" style="width:100%;">
                </div>
                <?php submit_button( $stlcf_edit_state ? esc_html__( 'Update', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Add New', 'sanirtech-lead-chat-forms' ) ); ?>
            </form>
        </div>
        <div style="flex:2;">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr><th>Name</th><th>Slug</th><th>Count</th></tr>
                </thead>
                <tbody>
                    <?php foreach ( $stlcf_categories as $stlcf_slug => $stlcf_data ) : 
                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
                        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
                        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        // phpcs:disable PluginCheck.Security.DirectDB.UnescapedDBParameter
                        $stlcf_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}stlcf_forms WHERE category = %s", $stlcf_slug ) );
                        // phpcs:enable
                        $stlcf_cat_del_nonce = wp_create_nonce( 'stlcf_delete_cat_' . $stlcf_slug );
                        ?>
                        <tr>
                            <td>
                                <strong><a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=edit_cat&cat_slug=' . $stlcf_slug ) ); ?>"><?php echo esc_html( $stlcf_data['name'] ); ?></a></strong>
                                <div class="row-actions">
                                    <span class="edit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=edit_cat&cat_slug=' . $stlcf_slug ) ); ?>"><?php esc_html_e( 'Edit', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                    <?php if ( $stlcf_slug !== 'general' ) : ?>
                                        | <span class="trash"><a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=delete_cat&cat_slug=' . $stlcf_slug . '&_wpnonce=' . $stlcf_cat_del_nonce ) ); ?>" style="color:#b91c1c;" onclick="return confirm('Confirm delete?');"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><code><?php echo esc_html( $stlcf_slug ); ?></code></td>
                            <td><?php echo esc_html( $stlcf_count ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>