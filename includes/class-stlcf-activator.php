<?php

// Abort if called directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fired during plugin activation to create necessary database tables.
 */
class STLCF_Activator {

    /**
     * Creates custom database tables for forms and entries.
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // 1. Table to store custom created forms structure
        $table_forms = $wpdb->prefix . 'stlcf_forms';
        $sql_forms = "CREATE TABLE $table_forms (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            fields text NOT NULL,
            category varchar(100) DEFAULT 'General' NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 2. Table to store submitted lead entries
        $table_entries = $wpdb->prefix . 'stlcf_entries';
        $sql_entries = "CREATE TABLE $table_entries (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            form_data text NOT NULL,
            page_url varchar(255) DEFAULT '' NOT NULL,
            submitted_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            KEY form_id (form_id)
        ) $charset_collate;";

        // 3. Table to store abandoned/partial lead entries
        $table_abandoned = $wpdb->prefix . 'stlcf_abandoned_leads';
        $sql_abandoned = "CREATE TABLE $table_abandoned (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            form_data text NOT NULL,
            page_url varchar(255) DEFAULT '' NOT NULL,
            ip_address varchar(100) DEFAULT '' NOT NULL,
            updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            KEY form_id (form_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // Execute queries safely using dbDelta
        dbDelta( $sql_forms );
        dbDelta( $sql_entries );
        dbDelta( $sql_abandoned );

        // Add redirect option trigger
        add_option( 'stlcf_do_activation_redirect', true );
    }
}