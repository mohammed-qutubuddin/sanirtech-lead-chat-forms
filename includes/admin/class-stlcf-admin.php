<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class STLCF_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
        add_action( 'admin_init', array( $this, 'register_general_settings' ) );
        add_filter( 'plugin_action_links_' . STLCF_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
        add_action( 'admin_init', array( $this, 'handle_create_form' ) );
        add_action( 'admin_init', array( $this, 'maybe_redirect_to_wizard' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_action_stlcf_export_leads_csv', array( $this, 'process_leads_csv_streaming_export' ) );
        add_action( 'admin_action_stlcf_print_receipt', array( $this, 'process_lead_print_receipt' ) );
        add_action( 'admin_head', array( $this, 'inject_admin_typography_patch' ) );
        add_action( 'admin_notices', array( $this, 'display_milestone_review_notice' ) );
        add_action( 'admin_init', array( $this, 'handle_dismiss_review_notice' ) );
    }

    public function inject_admin_typography_patch() {
        ?>
        <style id="stlcf-admin-typography-patch">
            .wrap, .wrap *, .wrap strong, .wrap b, strong, b {
                font-family: "Segoe UI", Arial, sans-serif !important;
            }
            .wrap code, .wrap code *, .wrap pre, .wrap pre *, .stlcf-shortcode-code {
                font-family: Consolas, Monaco, "Andale Mono", monospace !important;
            }
        </style>
        <?php
    }

    public function enqueue_admin_assets( $hook ) {
        wp_enqueue_script( 'jquery' );

        // Enqueue jQuery UI Sortable only on the 'Add New' page
        if ( strpos( $hook, 'stlcf-add-new' ) !== false ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }

        // Enqueue our admin custom CSS stylesheet with a cache-busting timestamp
        wp_enqueue_style(
            'stlcf-admin-style',
            STLCF_PLUGIN_URL . 'assets/admin/css/stlcf-admin.css',
            array(),
            STLCF_VERSION . '-' . time()
        );

        // Cleanly enqueue our dedicated external JS file from its new path
        wp_enqueue_script( 
            'stlcf-admin-script', 
            STLCF_PLUGIN_URL . 'assets/admin/js/stlcf-admin.js', 
            array( 'jquery' ), 
            STLCF_VERSION, 
            true 
        );

        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        // Unlocked for free version release
        $stlcf_is_pro = 1;
        wp_localize_script( 'stlcf-admin-script', 'stlcf_admin_vars', array(
            'is_pro' => $stlcf_is_pro
        ) );

        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_dashboard_en = isset( $stlcf_g_settings['enable_analytics_dashboard'] ) ? $stlcf_g_settings['enable_analytics_dashboard'] : '1';

        // Load Chart.js natively ONLY on the Analytics dashboard AND if the feature is enabled
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( $stlcf_dashboard_en === '1' && isset( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) === 'stlcf-analytics' ) {
            wp_enqueue_script( 'stlcf-chartjs', STLCF_PLUGIN_URL . 'assets/admin/js/chart.min.js', array(), '3.9.1', true );
        }
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
        add_submenu_page( 'stlcf-forms', esc_html__( 'Abandoned Leads', 'sanirtech-lead-chat-forms' ), esc_html__( 'Abandoned Leads', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-abandoned', array( $this, 'render_abandoned_page' ) );
        add_submenu_page( 'stlcf-forms', esc_html__( 'General Settings', 'sanirtech-lead-chat-forms' ), esc_html__( 'Settings', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-settings', array( $this, 'render_settings_page' ) );
        add_submenu_page( null, esc_html__( 'Setup Wizard', 'sanirtech-lead-chat-forms' ), esc_html__( 'Setup Wizard', 'sanirtech-lead-chat-forms' ), 'manage_options', 'stlcf-wizard', array( $this, 'render_setup_wizard_page' ) );
        $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
        $stlcf_dashboard_en = isset( $stlcf_g_settings['enable_analytics_dashboard'] ) ? $stlcf_g_settings['enable_analytics_dashboard'] : '1';

        // Only register the menu if the dashboard is enabled in settings
        if ( $stlcf_dashboard_en === '1' ) {
            add_submenu_page(
                'stlcf-forms',
                __( 'Analytics Dashboard', 'sanirtech-lead-chat-forms' ),
                __( 'Analytics', 'sanirtech-lead-chat-forms' ),
                'manage_options',
                'stlcf-analytics',
                array( $this, 'render_analytics_page' )
            );
        }
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
                } elseif ( $safe_key === 'multi_agents_list' && is_array( $value ) ) {
                    $sanitized_agents = array();
                    foreach ( $value as $a_idx => $agent ) {
                        $sanitized_agents[] = array(
                            'name'              => isset( $agent['name'] ) ? sanitize_text_field( $agent['name'] ) : '',
                            'title'             => isset( $agent['title'] ) ? sanitize_text_field( $agent['title'] ) : '',
                            'phone'             => isset( $agent['phone'] ) ? preg_replace( '/[^0-9]/', '', $agent['phone'] ) : '',
                            'status'            => isset( $agent['status'] ) ? sanitize_key( $agent['status'] ) : 'online',
                            'avatar'            => isset( $agent['avatar'] ) ? esc_url_raw( $agent['avatar'] ) : '',
                            'allowed_countries' => isset( $agent['allowed_countries'] ) ? sanitize_text_field( $agent['allowed_countries'] ) : ''
                        );
                    }
                    $sanitized[$safe_key] = maybe_serialize( $sanitized_agents );
                } elseif ( $safe_key === 'widget_faq_list' && is_array( $value ) ) {
                    $sanitized_faqs = array();
                    foreach ( $value as $faq ) {
                        $sanitized_faqs[] = array(
                            'question' => isset( $faq['question'] ) ? sanitize_text_field( $faq['question'] ) : '',
                            'answer'   => isset( $faq['answer'] ) ? sanitize_textarea_field( $faq['answer'] ) : ''
                        );
                    }
                    $sanitized[$safe_key] = maybe_serialize( $sanitized_faqs );
                } elseif ( in_array( $safe_key, array( 'btn_color', 'email_btn_color', 'float_btn_color' ), true ) ) {
                    $sanitized[$safe_key] = sanitize_hex_color( $value );
                } elseif ( in_array( $safe_key, array( 'gdpr_text', 'auto_responder_message', 'offline_message', 'hubspot_access_token' ), true ) ) {
                    $sanitized[$safe_key] = sanitize_textarea_field( $value );
                } else {
                    $sanitized[$safe_key] = sanitize_text_field( $value );
                }
            }
        }
        return $sanitized;
    }

    public function handle_create_form() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wpdb;
        $table_forms     = esc_sql( $wpdb->prefix . 'stlcf_forms' );
        $table_entries   = esc_sql( $wpdb->prefix . 'stlcf_entries' );
        $table_abandoned = esc_sql( $wpdb->prefix . 'stlcf_abandoned_leads' );

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $check_abandoned = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_abandoned ) );
        if ( $check_abandoned !== $table_abandoned ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql_abandoned = "CREATE TABLE $table_abandoned (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                form_id mediumint(9) NOT NULL,
                form_data text NOT NULL,
                page_url varchar(255) DEFAULT '' NOT NULL,
                ip_address varchar(100) DEFAULT '' NOT NULL,
                updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql_abandoned );
        }
        // phpcs:enable

        // Run database migration check dynamically to support layouts and custom overrides columns
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $existing_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_forms}`" );
        if ( ! is_array( $existing_columns ) ) {
            $existing_columns = array();
        }

        if ( ! in_array( 'layout', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `layout` VARCHAR(100) DEFAULT 'standard' NOT NULL AFTER `category`" );
        }
        if ( ! in_array( 'autoresponder_enabled', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `autoresponder_enabled` TINYINT(1) DEFAULT 0 NOT NULL AFTER `layout`" );
        }
        if ( ! in_array( 'autoresponder_subject', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `autoresponder_subject` TEXT DEFAULT '' NOT NULL AFTER `autoresponder_enabled`" );
        }
        if ( ! in_array( 'autoresponder_message', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `autoresponder_message` TEXT DEFAULT '' NOT NULL AFTER `autoresponder_subject`" );
        }
        if ( ! in_array( 'whatsapp_override', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `whatsapp_override` VARCHAR(100) DEFAULT '' NOT NULL AFTER `autoresponder_message`" );
        }
        if ( ! in_array( 'status', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `status` VARCHAR(20) DEFAULT 'active' NOT NULL AFTER `whatsapp_override`" );
        }
        if ( ! in_array( 'webhook_rules', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `webhook_rules` TEXT DEFAULT '' NOT NULL AFTER `status`" );
        }
        if ( ! in_array( 'email_rules', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `email_rules` TEXT DEFAULT '' NOT NULL AFTER `webhook_rules`" );
        }
        if ( ! in_array( 'agent_rotator', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `agent_rotator` TINYINT(1) DEFAULT 0 NOT NULL AFTER `email_rules`" );
        }
        if ( ! in_array( 'brand_color', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `brand_color` VARCHAR(20) DEFAULT '' NOT NULL AFTER `agent_rotator`" );
        }
        if ( ! in_array( 'button_text_color', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `button_text_color` VARCHAR(20) DEFAULT '' NOT NULL AFTER `brand_color`" );
        }
        if ( ! in_array( 'border_radius', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `border_radius` VARCHAR(20) DEFAULT '' NOT NULL AFTER `button_text_color`" );
        }
        if ( ! in_array( 'views', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `views` INT DEFAULT 0 NOT NULL AFTER `border_radius`" );
        }
        if ( ! in_array( 'conversions', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `conversions` INT DEFAULT 0 NOT NULL AFTER `views`" );
        }
        if ( ! in_array( 'ab_parent_id', $existing_columns, true ) ) {
            $wpdb->query( "ALTER TABLE `{$table_forms}` ADD `ab_parent_id` INT DEFAULT 0 NOT NULL AFTER `conversions`" );
        }

        // Performance database indexes migration
        $check_entries_index = $wpdb->get_results( "SHOW INDEX FROM `{$table_entries}` WHERE Key_name = 'form_id'" );
        if ( empty( $check_entries_index ) ) {
            $wpdb->query( "ALTER TABLE `{$table_entries}` ADD KEY `form_id` (`form_id`)" );
        }
        $check_abandoned_index = $wpdb->get_results( "SHOW INDEX FROM `{$table_abandoned}` WHERE Key_name = 'form_id'" );
        if ( empty( $check_abandoned_index ) ) {
            $wpdb->query( "ALTER TABLE `{$table_abandoned}` ADD KEY `form_id` (`form_id`)" );
        }
        // phpcs:enable

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
        if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'delete_abandoned' && isset( $_GET['entry_id'] ) ) {
            $delete_target_id = intval( wp_unslash( $_GET['entry_id'] ) );
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_delete_abandoned_' . $delete_target_id ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    $wpdb->delete( $table_abandoned, array( 'id' => $delete_target_id ), array( '%d' ) );
                    // phpcs:enable
                    wp_safe_redirect( admin_url( 'admin.php?page=stlcf-abandoned&status=deleted' ) );
                    exit;
                }
            }
        }

        if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'toggle_status' && isset( $_GET['form_id'] ) ) {
            $form_id = intval( wp_unslash( $_GET['form_id'] ) );
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_toggle_status_' . $form_id ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $current_status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table_forms} WHERE id = %d", $form_id ) );
                    $new_status = ( $current_status === 'inactive' ) ? 'active' : 'inactive';
                    $wpdb->update( $table_forms, array( 'status' => $new_status ), array( 'id' => $form_id ), array( '%s' ), array( '%d' ) );
                    // phpcs:enable
                    wp_safe_redirect( admin_url( 'admin.php?page=stlcf-forms&status=status_toggled' ) );
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
            $form_layout = isset( $_POST['form_layout'] ) ? sanitize_key( wp_unslash( $_POST['form_layout'] ) ) : 'standard';
            
            $submitted_fields = array();
            if ( isset( $_POST['stlcf_fields'] ) && is_array( $_POST['stlcf_fields'] ) ) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $raw_fields = wp_unslash( $_POST['stlcf_fields'] );
                foreach ( $raw_fields as $field ) {
                    $submitted_fields[] = array(
                        'type'          => isset( $field['type'] ) ? sanitize_text_field( $field['type'] ) : 'text',
                        'label'         => isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '',
                        'required'      => isset( $field['required'] ) ? 1 : 0,
                        'routing'       => isset( $field['routing'] ) ? sanitize_textarea_field( $field['routing'] ) : '',
                        'cond_enabled'  => isset( $field['cond_enabled'] ) ? 1 : 0,
                        'cond_field'    => isset( $field['cond_field'] ) ? sanitize_text_field( $field['cond_field'] ) : '',
                        'cond_operator' => isset( $field['cond_operator'] ) ? sanitize_key( $field['cond_operator'] ) : 'equals',
                        'cond_value'    => isset( $field['cond_value'] ) ? sanitize_text_field( $field['cond_value'] ) : ''
                    );
                }
            }

            $autoresponder_enabled = isset( $_POST['autoresponder_enabled'] ) ? 1 : 0;
            $autoresponder_subject = isset( $_POST['autoresponder_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['autoresponder_subject'] ) ) : '';
            $autoresponder_message = isset( $_POST['autoresponder_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['autoresponder_message'] ) ) : '';
            $whatsapp_override     = isset( $_POST['whatsapp_override'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp_override'] ) ) : '';
            $webhook_rules         = isset( $_POST['webhook_rules'] ) ? sanitize_textarea_field( wp_unslash( $_POST['webhook_rules'] ) ) : '';
            $email_rules           = isset( $_POST['email_rules'] ) ? sanitize_textarea_field( wp_unslash( $_POST['email_rules'] ) ) : '';
            
            $agent_rotator         = isset( $_POST['agent_rotator'] ) ? 1 : 0;
            $brand_color           = isset( $_POST['brand_color'] ) ? sanitize_text_field( wp_unslash( $_POST['brand_color'] ) ) : '';
            $button_text_color     = isset( $_POST['button_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['button_text_color'] ) ) : '';
            $border_radius         = isset( $_POST['border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['border_radius'] ) ) : '';
            $ab_parent_id          = isset( $_POST['ab_parent_id'] ) ? intval( wp_unslash( $_POST['ab_parent_id'] ) ) : 0;

            $data_payload = array( 
                'title'                  => $form_title, 
                'fields'                 => maybe_serialize( $submitted_fields ), 
                'category'               => $form_category,
                'layout'                 => $form_layout,
                'autoresponder_enabled'  => $autoresponder_enabled,
                'autoresponder_subject'  => $autoresponder_subject,
                'autoresponder_message'  => $autoresponder_message,
                'whatsapp_override'      => $whatsapp_override,
                'webhook_rules'          => $webhook_rules,
                'email_rules'            => $email_rules,
                'agent_rotator'          => $agent_rotator,
                'brand_color'            => $brand_color,
                'button_text_color'      => $button_text_color,
                'border_radius'          => $border_radius,
                'ab_parent_id'           => $ab_parent_id
            );
            
            if ( isset( $_POST['form_id'] ) && ! empty( $_POST['form_id'] ) ) {
                $form_id = intval( wp_unslash( $_POST['form_id'] ) );
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->update( $table_forms, $data_payload, array( 'id' => $form_id ), array( '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d' ), array( '%d' ) );
                // phpcs:enable
                $this->register_translatable_strings( $form_id, $form_title, $submitted_fields );
                wp_safe_redirect( admin_url( 'admin.php?page=stlcf-forms&status=updated' ) );
            } else {
                $data_payload['created_at'] = current_time( 'mysql' );
                $data_payload['views']       = 0;
                $data_payload['conversions'] = 0;
                // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->insert( $table_forms, $data_payload, array( '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%d' ) );
                $form_id = $wpdb->insert_id;
                // phpcs:enable
                $this->register_translatable_strings( $form_id, $form_title, $submitted_fields );
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
    public function render_abandoned_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-abandoned.php'; }
    public function render_settings_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-settings.php'; }
    public function render_setup_wizard_page() { require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-setup-wizard.php'; }

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
        $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
        $end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
        
        $query = "SELECT e.*, f.title as form_title FROM {$wpdb->prefix}stlcf_entries e LEFT JOIN {$wpdb->prefix}stlcf_forms f ON e.form_id = f.id WHERE 1=1";
        $params = array();

        if ( $form_id_filter > 0 ) {
            $query .= " AND e.form_id = %d";
            $params[] = $form_id_filter;
        }

        if ( ! empty( $start_date ) ) {
            $query .= " AND e.submitted_at >= %s";
            $params[] = $start_date . ' 00:00:00';
        }

        if ( ! empty( $end_date ) ) {
            $query .= " AND e.submitted_at <= %s";
            $params[] = $end_date . ' 23:59:59';
        }

        $query .= " ORDER BY e.id DESC";

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        if ( ! empty( $params ) ) {
            $stlcf_dataset = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );
        } else {
            $stlcf_dataset = $wpdb->get_results( $query, ARRAY_A );
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

    public function render_analytics_page() {
        require_once STLCF_PLUGIN_DIR . 'includes/admin/views/view-analytics.php';
    }

    private function register_translatable_strings( $form_id, $title, $fields ) {
        $this->register_single_string( 'Form Title - ' . $form_id, $title );
        
        if ( is_array( $fields ) ) {
            foreach ( $fields as $idx => $field ) {
                $label = isset( $field['label'] ) ? $field['label'] : '';
                if ( ! empty( $label ) ) {
                    $this->register_single_string( 'Form ' . $form_id . ' Field ' . $idx . ' Label', $label );
                }
                
                $routing = isset( $field['routing'] ) ? $field['routing'] : '';
                if ( ! empty( $routing ) ) {
                    $lines = explode( "\n", $routing );
                    foreach ( $lines as $line_idx => $line ) {
                        $parts = explode( '|', trim( $line ) );
                        $name = isset( $parts[0] ) ? trim( $parts[0] ) : '';
                        if ( ! empty( $name ) ) {
                            $this->register_single_string( 'Form ' . $form_id . ' Field ' . $idx . ' Agent Name ' . $line_idx, $name );
                        }
                    }
                }
            }
        }
    }

    private function register_single_string( $name, $value ) {
        if ( function_exists( 'icl_register_string' ) ) {
            icl_register_string( 'sanirtech-lead-chat-forms', $name, $value );
        }
        if ( function_exists( 'pll_register_string' ) ) {
            pll_register_string( $name, $value, 'sanirtech-lead-chat-forms' );
        }
    }

    public function maybe_redirect_to_wizard() {
        if ( get_option( 'stlcf_do_activation_redirect' ) ) {
            delete_option( 'stlcf_do_activation_redirect' );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( ! isset( $_GET['activate-multi'] ) && current_user_can( 'manage_options' ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=stlcf-wizard' ) );
                exit;
            }
        }
    }

    public function process_lead_print_receipt() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions to print lead details.', 'sanirtech-lead-chat-forms' ) );
        }

        if ( ! isset( $_GET['_wpnonce'] ) || ! isset( $_GET['entry_id'] ) ) {
            wp_die( esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) );
        }
        $entry_id = intval( wp_unslash( $_GET['entry_id'] ) );
        if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_print_entry_' . $entry_id ) ) {
            wp_die( esc_html__( 'Security check failed!', 'sanirtech-lead-chat-forms' ) );
        }

        global $wpdb;
        $table_entries = esc_sql( $wpdb->prefix . 'stlcf_entries' );
        $table_forms   = esc_sql( $wpdb->prefix . 'stlcf_forms' );

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $entry = $wpdb->get_row( $wpdb->prepare(
            "SELECT e.*, f.title as form_title FROM {$table_entries} e LEFT JOIN {$table_forms} f ON e.form_id = f.id WHERE e.id = %d",
            $entry_id
        ) );
        // phpcs:enable

        if ( ! $entry ) {
            wp_die( esc_html__( 'Requested lead record not found.', 'sanirtech-lead-chat-forms' ) );
        }

        $fields = maybe_unserialize( $entry->form_data );
        /* translators: %d: Deleted form identifier ID */
        $form_title = ! empty( $entry->form_title ) ? $entry->form_title : sprintf( __( 'Deleted Form (ID: %d)', 'sanirtech-lead-chat-forms' ), $entry->form_id );

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="UTF-8">
            <title><?php
                /* translators: %d: Lead entry serial ID number */
                echo esc_html( sprintf( __( 'Lead Receipt #%d', 'sanirtech-lead-chat-forms' ), $entry->id ) );
            ?></title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    color: #1e293b;
                    background: #fff;
                    margin: 0;
                    padding: 40px;
                }
                .receipt-container {
                    max-width: 600px;
                    margin: 0 auto;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 30px;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                }
                .header {
                    border-bottom: 2px solid #3b82f6;
                    padding-bottom: 15px;
                    margin-bottom: 25px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 20px;
                    color: #0f172a;
                }
                .header .meta-row {
                    display: flex;
                    justify-content: space-between;
                    font-size: 13px;
                    color: #64748b;
                    margin-top: 8px;
                }
                .meta-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 25px;
                }
                .meta-table th, .meta-table td {
                    text-align: left;
                    padding: 10px 12px;
                    font-size: 14px;
                    border-bottom: 1px solid #f1f5f9;
                }
                .meta-table th {
                    font-weight: 600;
                    color: #475569;
                    background: #f8fafc;
                    width: 40%;
                }
                .signature-img {
                    max-height: 80px;
                    border: 1px dashed #cbd5e1;
                    border-radius: 6px;
                    background: #f8fafc;
                    padding: 5px;
                }
                .footer {
                    text-align: center;
                    font-size: 11px;
                    color: #94a3b8;
                    margin-top: 30px;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 15px;
                }
                @media print {
                    body {
                        padding: 0;
                    }
                    .receipt-container {
                        border: none;
                        box-shadow: none;
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="header">
                    <h1><?php echo esc_html( $form_title ); ?></h1>
                    <div class="meta-row">
                        <span><strong><?php esc_html_e( 'Lead ID:', 'sanirtech-lead-chat-forms' ); ?></strong> #<?php echo esc_html( $entry->id ); ?></span>
                        <span><strong><?php esc_html_e( 'Date:', 'sanirtech-lead-chat-forms' ); ?></strong> <?php echo esc_html( $entry->submitted_at ); ?></span>
                    </div>
                </div>

                <table class="meta-table">
                    <tbody>
                        <?php 
                        if ( is_array( $fields ) ) {
                            foreach ( $fields as $lbl => $val ) {
                                ?>
                                <tr>
                                    <th><?php echo esc_html( $lbl ); ?></th>
                                    <td>
                                        <?php 
                                        if ( filter_var( $val, FILTER_VALIDATE_URL ) && ( strpos( $val, '.png' ) !== false || strpos( $val, '.jpg' ) !== false || strpos( $val, '.jpeg' ) !== false || strpos( $val, '.gif' ) !== false ) ) {
                                            if ( strpos( $val, 'signature_' ) !== false ) {
                                                echo '<img src="' . esc_url( $val ) . '" class="signature-img" alt="Signature">';
                                            } else {
                                                echo '<a href="' . esc_url( $val ) . '" target="_blank">' . esc_html__( 'View Image Attachment', 'sanirtech-lead-chat-forms' ) . '</a>';
                                            }
                                        } else {
                                            echo esc_html( $val );
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <?php if ( ! empty( $entry->page_url ) ) : ?>
                            <tr>
                                <th><?php esc_html_e( 'Submitted URL Context', 'sanirtech-lead-chat-forms' ); ?></th>
                                <td><a href="<?php echo esc_url( $entry->page_url ); ?>" target="_blank" style="word-break: break-all;"><?php echo esc_html( $entry->page_url ); ?></a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="footer">
                    <?php
                    /* translators: %s: WordPress site name bloginfo */
                    echo esc_html( sprintf( __( 'Receipt generated automatically by %s.', 'sanirtech-lead-chat-forms' ), get_bloginfo( 'name' ) ) );
                    ?>
                </div>
            </div>

            <script type="text/javascript">
                window.onload = function() {
                    window.print();
                }
            </script>
        </body>
        </html>
        <?php
        exit;
    }

    public function display_milestone_review_notice() {
        if ( get_option( 'stlcf_review_notice_dismissed' ) === '1' ) {
            return;
        }

        global $wpdb;
        $table_entries = esc_sql( $wpdb->prefix . 'stlcf_entries' );

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $leads_count = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$table_entries}" );
        // phpcs:enable

        // Trigger only after 50 leads captured
        if ( $leads_count < 50 ) {
            return;
        }

        $dismiss_url = wp_nonce_url( add_query_arg( 'stlcf_dismiss_review', '1' ), 'stlcf_dismiss_review_nonce' );
        ?>
        <div class="notice notice-info is-dismissible stlcf-review-notice" style="border-left-color: #10b981; padding: 15px; position: relative;">
            <div style="display: flex; align-items: flex-start; gap: 15px;">
                <span class="dashicons dashicons-thumbs-up" style="color: #10b981; font-size: 36px; width: 36px; height: 36px; margin-top: 5px;"></span>
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #1e293b; font-weight: 700; font-size: 16px;"><?php esc_html_e( '🎉 You\'ve captured over 50 leads with SanirTech Lead Chat Forms!', 'sanirtech-lead-chat-forms' ); ?></h3>
                    <p style="margin: 0 0 12px 0; font-size: 13px; color: #475569; line-height: 1.5;">
                        <?php esc_html_e( 'Your WhatsApp lead forms are actively converting website traffic. Would you mind supporting our development by leaving a quick 5-star review on WordPress.org? It helps us reach more developers like you!', 'sanirtech-lead-chat-forms' ); ?>
                    </p>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="https://wordpress.org/support/plugin/sanirtech-lead-chat-forms/reviews/#new-post" target="_blank" class="button button-primary" style="background: #10b981; border-color: #10b981; font-weight: 600;"><?php esc_html_e( '⭐⭐⭐⭐⭐ Review Now', 'sanirtech-lead-chat-forms' ); ?></a>
                        <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary"><?php esc_html_e( 'I already left a review', 'sanirtech-lead-chat-forms' ); ?></a>
                        <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-link" style="color: #64748b; text-decoration: none;"><?php esc_html_e( 'Dismiss / Maybe later', 'sanirtech-lead-chat-forms' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function handle_dismiss_review_notice() {
        if ( isset( $_GET['stlcf_dismiss_review'] ) && $_GET['stlcf_dismiss_review'] === '1' ) {
            if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'stlcf_dismiss_review_nonce' ) ) {
                if ( current_user_can( 'manage_options' ) ) {
                    update_option( 'stlcf_review_notice_dismissed', '1' );
                    wp_safe_redirect( remove_query_arg( array( 'stlcf_dismiss_review', '_wpnonce' ) ) );
                    exit;
                }
            }
        }
    }
}

