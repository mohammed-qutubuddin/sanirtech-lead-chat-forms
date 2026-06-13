<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class STLCF_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
        add_action( 'admin_init', array( $this, 'register_general_settings' ) );
        add_filter( 'plugin_action_links_' . STLCF_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
        add_action( 'admin_init', array( $this, 'handle_create_form' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_action_stlcf_export_leads_csv', array( $this, 'process_leads_csv_streaming_export' ) );
    }

    public function enqueue_admin_assets( $hook ) {
        // Only load assets on our specific plugin pages to avoid conflicts
        if ( strpos( $hook, 'stlcf-' ) === false && $hook !== 'toplevel_page_stlcf-forms' ) {
            return;
        }

        wp_enqueue_script( 'jquery' );

        // Enqueue jQuery UI Sortable only on the 'Add New' page
        if ( strpos( $hook, 'stlcf-add-new' ) !== false ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }

        // Enqueue our admin custom CSS stylesheet
        wp_enqueue_style(
            'stlcf-admin-style',
            STLCF_PLUGIN_URL . 'assets/admin/css/stlcf-admin.css',
            array(),
            STLCF_VERSION
        );

        // Cleanly enqueue our dedicated external JS file from its new path
        wp_enqueue_script( 
            'stlcf-admin-script', 
            STLCF_PLUGIN_URL . 'assets/admin/js/stlcf-admin.js', 
            array( 'jquery' ), 
            STLCF_VERSION, 
            true 
        );
    }

    public function get_all_categories() {
        $default_seeds = array(
            'general' => array( 'name' => 'General Forms', 'slug' => 'general' ),
            'sales'   => array( 'name' => 'Sales Leads', 'slug' => 'sales' ),
            'support' => array( 'name' => 'Customer Support', 'slug' => 'support' ),
        );
        return get_option( 'stlcf_form_categories', $default_seeds );
    }

    public function add_plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=stlcf-settings">' . esc_html__( 'Settings', 'sanirtech-lead-chat-forms' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function register_admin_menus() {
        add_menu_page( esc_html__( 'SanirTech Forms', 'sanirtech-lead-chat-forms' ), esc_html__( 'SanirTech Forms', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-forms', array( $this, 'render_forms_page' ), 'dashicons-format-chat', 85 );
        add_submenu_page( 'stlcf-forms', esc_html__( 'All Forms', 'sanirtech-lead-chat-forms' ), esc_html__( 'All Forms', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-forms', array( $this, 'render_forms_page' ) );
        add_submenu_page( 'stlcf-forms', esc_html__( 'Add New Form', 'sanirtech-lead-chat-forms' ), esc_html__( 'Add New', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-add-new', array( $this, 'render_add_new_page' ) );
        add_submenu_page( 'stlcf-forms', esc_html__( 'Form Categories', 'sanirtech-lead-chat-forms' ), esc_html__( 'Categories', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-categories', array( $this, 'render_categories_page' ) );
        add_submenu_page( 'stlcf-forms', esc_html__( 'Form Entries', 'sanirtech-lead-chat-forms' ), esc_html__( 'Entries', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-entries', array( $this, 'render_entries_page' ) );
        add_submenu_page( 'stlcf-forms', esc_html__( 'General Settings', 'sanirtech-lead-chat-forms' ), esc_html__( 'Settings', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-settings', array( $this, 'render_settings_page' ) );
    }

    public function register_general_settings() {
        register_setting( 'stlcf_settings_group', 'stlcf_general_settings', array( 'sanitize_callback' => array( $this, 'sanitize_general_settings' ) ) );
        add_settings_section( 'stlcf_general_section', esc_html__( 'Configure Global Settings', 'sanirtech-lead-chat-forms' ), null, 'stlcf-settings-admin' );
    }

    public function sanitize_general_settings( $input ) {
        $sanitized = array();
        if ( is_array( $input ) ) {
            foreach ( $input as $key => $value ) {
                $safe_key = sanitize_key( $key );
                if ( $safe_key === 'admin_email_receiver' ) {
                    $sanitized[$safe_key] = sanitize_email( $value );
                } elseif ( in_array( $safe_key, array( 'btn_color', 'email_btn_color', 'float_btn_color' ), true ) ) {
                    $sanitized[$safe_key] = sanitize_hex_color( $value );
                } else {
                    $sanitized[$safe_key] = sanitize_text_field( $value );
                }
            }
        }
        return $sanitized;
    }

    public function handle_create_form() {
        global $wpdb;
        $table_forms   = $wpdb->prefix . 'stlcf_forms';
        $table_entries = $wpdb->prefix . 'stlcf_entries';

        if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'delete_form' && isset( $_GET['form_id'] ) ) {
            $delete_target_id = intval( wp_unslash( $_GET['form_id'] ) );
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_delete_form_' . $delete_target_id ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    $wpdb->delete( $table_forms, array( 'id' => $delete_target_id ), array( '%d' ) );
                    $wpdb->delete( $table_entries, array( 'form_id' => $delete_target_id ), array( '%d' ) );
                    // phpcs:enable
                    wp_safe_redirect( admin_url( 'admin.php?page=stlcf-forms&status=deleted' ) );
                    exit;
                }
            }
        }

        if ( isset( $_POST['stlcf_action'] ) && sanitize_key( wp_unslash( $_POST['stlcf_action'] ) ) === 'save_form' ) {
            if ( ! isset( $_POST['stlcf_form_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_form_nonce'] ) ), 'stlcf_save_form_action' ) ) {
                wp_die( esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) );
            }
            
            $form_title = isset( $_POST['form_title'] ) ? sanitize_text_field( wp_unslash( $_POST['form_title'] ) ) : '';
            $form_category = isset( $_POST['form_category'] ) ? sanitize_text_field( wp_unslash( $_POST['form_category'] ) ) : 'general';
            
            $submitted_fields = array();
            if ( isset( $_POST['stlcf_fields'] ) && is_array( $_POST['stlcf_fields'] ) ) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $raw_fields = wp_unslash( $_POST['stlcf_fields'] );
                foreach ( $raw_fields as $field ) {
                    $submitted_fields[] = array(
                        'type'     => isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text',
                        'label'    => isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '',
                        'required' => isset( $field['required'] ) ? 1 : 0,
                        // PCP SAFE: Sanitizing multi-line routing configurations parameters cleanly
                        'routing'  => isset( $field['routing'] ) ? sanitize_textarea_field( $field['routing'] ) : ''
                    );
                }
            }

            $data_payload = array( 'title' => $form_title, 'fields' => maybe_serialize( $submitted_fields ), 'category' => $form_category );
            
            if ( isset( $_POST['form_id'] ) && ! empty( $_POST['form_id'] ) ) {
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->update( $table_forms, $data_payload, array( 'id' => intval( wp_unslash( $_POST['form_id'] ) ) ), array( '%s', '%s', '%s' ), array( '%d' ) );
                // phpcs:enable
                wp_safe_redirect( admin_url( 'admin.php?page=stlcf-forms&status=updated' ) );
            } else {
                $data_payload['created_at'] = current_time( 'mysql' );
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->insert( $table_forms, $data_payload, array( '%s', '%s', '%s', '%s' ) );
                // phpcs:enable
                wp_safe_redirect( admin_url( 'admin.php?page=stlcf-forms&status=success' ) );
            }
            exit;
        }

        if ( isset( $_POST['stlcf_cat_action'] ) && sanitize_key( wp_unslash( $_POST['stlcf_cat_action'] ) ) === 'save_category' ) {
            if ( ! isset( $_POST['stlcf_cat_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['stlcf_cat_nonce'] ) ), 'stlcf_save_cat_action' ) ) { 
                wp_die( esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) ); 
            }
            
            $cat_name = isset( $_POST['cat_name'] ) ? sanitize_text_field( wp_unslash( $_POST['cat_name'] ) ) : '';
            $cat_slug = isset( $_POST['cat_slug'] ) ? sanitize_title( wp_unslash( $_POST['cat_slug'] ) ) : '';
            if ( empty( $cat_slug ) ) { $cat_slug = sanitize_title( $cat_name ); }
            
            $categories = $this->get_all_categories();
            if ( isset( $_POST['old_slug'] ) && ! empty( $_POST['old_slug'] ) ) {
                $old_slug = sanitize_title( wp_unslash( $_POST['old_slug'] ) );
                unset( $categories[$old_slug] );
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->update( $table_forms, array( 'category' => $cat_slug ), array( 'category' => $old_slug ), array( '%s' ), array( '%s' ) );
                // phpcs:enable
            }
            $categories[$cat_slug] = array( 'name' => $cat_name, 'slug' => $cat_slug );
            update_option( 'stlcf_form_categories', $categories );
            wp_safe_redirect( admin_url( 'admin.php?page=stlcf-categories&status=cat_saved' ) );
            exit;
        }

        if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'delete_cat' && isset( $_GET['cat_slug'] ) ) {
            $delete_slug = sanitize_title( wp_unslash( $_GET['cat_slug'] ) );
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_delete_cat_' . $delete_slug ) ) {
                if ( current_user_can( 'manage_options' ) && $delete_slug !== 'general' ) {
                    $categories = $this->get_all_categories();
                    unset( $categories[$delete_slug] );
                    update_option( 'stlcf_form_categories', $categories );
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    $wpdb->update( $table_forms, array( 'category' => 'general' ), array( 'category' => $delete_slug ), array( '%s' ), array( '%s' ) );
                    // phpcs:enable
                    wp_safe_redirect( admin_url( 'admin.php?page=stlcf-categories&status=cat_deleted' ) );
                    exit;
                }
            }
        }
    }

    public function render_forms_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-forms.php'; }
    public function render_add_new_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-add-new.php'; }
    public function render_categories_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-categories.php'; }
    public function render_entries_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-entries.php'; }
    public function render_settings_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-settings.php'; }

    /**
     * PREMIUM DATA OPERATIONS MODULE: Secure, lightning fast pipeline streamer array loop parser.
     * Operates cleanly across low footprint limits bounds without breaking server threads execution loops.
     */
    public function process_leads_csv_streaming_export() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized security parameters access allocation denied.', 'sanirtech-lead-chat-forms' ) );
        }

        // Verify cryptographic origin authenticity nonces bounds
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_export_csv_action' ) ) {
            wp_die( esc_html__( 'Security footprint validation mapping sequence has failed.', 'sanirtech-lead-chat-forms' ) );
        }

        global $wpdb;

        $form_id_filter = isset( $_GET['form_id'] ) ? intval( wp_unslash( $_GET['form_id'] ) ) : 0;
        
        // Fetching structural targets segments datasets entries arrays maps
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
        if ( $form_id_filter > 0 ) {
            $stlcf_dataset = $wpdb->get_results( $wpdb->prepare( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id WHERE e.form_id = %d ORDER BY e.id DESC", $form_id_filter ), ARRAY_A );
        } else {
            $stlcf_dataset = $wpdb->get_results( "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id ORDER BY e.id DESC", ARRAY_A );
        }
        // phpcs:enable

        if ( empty( $stlcf_dataset ) ) {
            wp_die( esc_html__( 'No available lead logging rows discovered to extract datasets records.', 'sanirtech-lead-chat-forms' ) );
        }

        // Set proper browser stream download payload boundary envelopes headers directives
        $csv_file_identity = 'stlcf_leads_export_' . current_time( 'Y-m-d_His' ) . '.csv';
        
        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $csv_file_identity ) . '";' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        // Open light tracking file mapping descriptor pointer reference stream pipeline
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Required for performance safe memory output streaming
        $output_stream_channel = fopen( 'php://output', 'w' );

        // Fix Excel cell language UTF-8 character conversion breakage symbols
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Writing stream buffer directly
        fwrite( $output_stream_channel, chr(0xEF) . chr(0xBB) . chr(0xBF) );

        // Establish strict standard header layouts matrix rows
        $csv_structural_headers = array(
            __( 'Lead System ID', 'sanirtech-lead-chat-forms' ),
            __( 'Source Form Name', 'sanirtech-lead-chat-forms' ),
            __( 'User Log Data Payload', 'sanirtech-lead-chat-forms' ),
            __( 'Origin Referral URL', 'sanirtech-lead-chat-forms' ),
            __( 'Submission Date-Time', 'sanirtech-lead-chat-forms' )
        );
        fputcsv( $output_stream_channel, $csv_structural_headers );

        // Process loop streams array nodes entries lines cleanly row-by-row
        foreach ( $stlcf_dataset as $lead_data_row ) {
            $raw_serialized_fields = maybe_unserialize( $lead_data_row['form_data'] );
            $compiled_text_inline_payload = "";

            if ( is_array( $raw_serialized_fields ) ) {
                $item_pairs = array();
                foreach ( $raw_serialized_fields as $data_lbl => $data_val ) {
                    $item_pairs[] = trim( $data_lbl ) . ': ' . trim( $data_val );
                }
                // Separate fields cleanly with visible delimiter pipelines symbols
                $compiled_text_inline_payload = implode( ' | ', $item_pairs );
            } else {
                $compiled_text_inline_payload = $lead_data_row['form_data'];
            }

            $csv_sanitized_row_line = array(
                $lead_data_row['id'],
                ! empty( $lead_data_row['form_title'] ) ? $lead_data_row['form_title'] : __( 'Unidentified Form Source', 'sanirtech-lead-chat-forms' ),
                $compiled_text_inline_payload,
                $lead_data_row['page_url'],
                $lead_data_row['submitted_at']
            );

            fputcsv( $output_stream_channel, $csv_sanitized_row_line );
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing active stream buffer
        fclose( $output_stream_channel );
        exit;
    }
}

