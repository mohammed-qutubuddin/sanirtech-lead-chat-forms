<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$stlcf_categories = $this->get_all_categories();
$stlcf_edit_slug  = '';
$stlcf_edit_name  = '';
$stlcf_edit_state = false;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'edit_cat' && isset( $_GET['cat_slug'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $stlcf_edit_slug = sanitize_title( wp_unslash( $_GET['cat_slug'] ) );
    if ( isset( $stlcf_categories[$stlcf_edit_slug] ) ) {
        $stlcf_edit_state = true;
        $stlcf_edit_name  = $stlcf_categories[$stlcf_edit_slug]['name'];
    }
}

// PERFORMANCE OPTIMIZATION: Fetch form distribution counters grouped by categories in one execution
$stlcf_cat_counts = array();
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
$stlcf_raw_counts = $wpdb->get_results( "SELECT category, COUNT(*) as count FROM {$wpdb->prefix}stlcf_forms GROUP BY category", ARRAY_A );
// phpcs:enable

if ( is_array( $stlcf_raw_counts ) ) {
    foreach ( $stlcf_raw_counts as $stlcf_row ) {
        $stlcf_cat_counts[ $stlcf_row['category'] ] = intval( $stlcf_row['count'] );
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
    
    <div class="stlcf-categories-layout">
        
        <div class="stlcf-categories-form-pane stlcf-card">
            <div class="stlcf-card-header">
                <h2><?php echo $stlcf_edit_state ? esc_html__( 'Edit Category', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Add Category', 'sanirtech-lead-chat-forms' ); ?></h2>
            </div>
            <div class="stlcf-card-body">
                <form method="POST" action="">
                    <?php wp_nonce_field( 'stlcf_save_cat_action', 'stlcf_cat_nonce' ); ?>
                    <input type="hidden" name="stlcf_cat_action" value="save_category">
                    <?php if ( $stlcf_edit_state ) : ?>
                        <input type="hidden" name="old_slug" value="<?php echo esc_attr( $stlcf_edit_slug ); ?>">
                    <?php endif; ?>
                    
                    <div class="stlcf-form-row-group">
                        <label for="stlcf_cat_name"><?php esc_html_e( 'Name', 'sanirtech-lead-chat-forms' ); ?></label>
                        <input type="text" id="stlcf_cat_name" name="cat_name" value="<?php echo esc_attr( $stlcf_edit_name ); ?>" required>
                    </div>
                    
                    <div class="stlcf-form-row-group stlcf-mb-md">
                        <label for="stlcf_cat_slug"><?php esc_html_e( 'Slug', 'sanirtech-lead-chat-forms' ); ?></label>
                        <input type="text" id="stlcf_cat_slug" name="cat_slug" value="<?php echo esc_attr( $stlcf_edit_slug ); ?>">
                    </div>
                    
                    <?php submit_button( $stlcf_edit_state ? esc_html__( 'Update Category', 'sanirtech-lead-chat-forms' ) : esc_html__( 'Add New Category', 'sanirtech-lead-chat-forms' ), 'primary', 'submit', false ); ?>
                </form>
            </div>
        </div>
        
        <div class="stlcf-categories-table-pane">
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'sanirtech-lead-chat-forms' ); ?></th>
                        <th><?php esc_html_e( 'Slug', 'sanirtech-lead-chat-forms' ); ?></th>
                        <th class="stlcf-text-center"><?php esc_html_e( 'Forms Count', 'sanirtech-lead-chat-forms' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $stlcf_categories as $stlcf_slug => $stlcf_data ) : 
                        // Fetching local cached values safely instead of inline sub-query execution
                        $stlcf_count = isset( $stlcf_cat_counts[$stlcf_slug] ) ? $stlcf_cat_counts[$stlcf_slug] : 0;
                        $stlcf_cat_del_nonce = wp_create_nonce( 'stlcf_delete_cat_' . $stlcf_slug );
                        ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=edit_cat&cat_slug=' . $stlcf_slug ) ); ?>" class="stlcf-form-title-link">
                                        <?php echo esc_html( $stlcf_data['name'] ); ?>
                                    </a>
                                </strong>
                                <div class="row-actions stlcf-row-actions-align">
                                    <span class="edit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=edit_cat&cat_slug=' . $stlcf_slug ) ); ?>"><?php esc_html_e( 'Edit', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                    <?php if ( $stlcf_slug !== 'general' ) : ?>
                                         | <span class="trash"><a href="<?php echo esc_url( admin_url( 'admin.php?page=stlcf-categories&action=delete_cat&cat_slug=' . $stlcf_slug . '&_wpnonce=' . $stlcf_cat_del_nonce ) ); ?>" class="stlcf-trash-link" onclick="return confirm('<?php esc_attr_e('Confirm delete? All forms in this category will move to General.', 'sanirtech-lead-chat-forms'); ?>');"><?php esc_html_e( 'Delete', 'sanirtech-lead-chat-forms' ); ?></a></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><code><?php echo esc_html( $stlcf_slug ); ?></code></td>
                            <td class="stlcf-text-center">
                                <span class="stlcf-cat-counter-badge"><?php echo esc_html( $stlcf_count ); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>