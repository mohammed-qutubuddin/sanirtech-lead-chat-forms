<?php
/**
 * Plugin Name:       SanirTech Lead Chat Forms
 * Description:       Secure, high-converting lead capture form builder with direct chat routing.
 * Version:           1.0.1
 * Author:            Abdul Nasir
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sanirtech-lead-chat-forms
 * Requires PHP:      7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'STLCF_VERSION', '1.0.1' );
define( 'STLCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STLCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STLCF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

function stlcf_activate_plugin() {
    require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-activator.php';
    stlcf_Activator::activate();
}
register_activation_hook( __FILE__, 'stlcf_activate_plugin' );

function stlcf_run_plugin() {
    require_once STLCF_PLUGIN_DIR . 'includes/class-stlcf-core.php';
    $stlcf_plugin = new stlcf_Core();
    $stlcf_plugin->run();
}

// ==========================================================================
// AUTOMATED GDPR DATA RETENTION CRON ENGINE
// ==========================================================================

// 1. Setup and schedule the cron event dynamically
add_action( 'init', 'stlcf_setup_gdpr_retention_cron' );
function stlcf_setup_gdpr_retention_cron() {
    $stlcf_options  = get_option( 'stlcf_general_settings', array() );
    $stlcf_cron_en  = isset( $stlcf_options['enable_gdpr_cron'] ) ? $stlcf_options['enable_gdpr_cron'] : '0';

    if ( $stlcf_cron_en === '1' ) {
        // Schedule it daily if it doesn't exist
        if ( ! wp_next_scheduled( 'stlcf_daily_gdpr_cleanup_event' ) ) {
            wp_schedule_event( time(), 'daily', 'stlcf_daily_gdpr_cleanup_event' );
        }
    } else {
        // Unschedule and destroy the cron if the admin turns the setting OFF
        $stlcf_timestamp = wp_next_scheduled( 'stlcf_daily_gdpr_cleanup_event' );
        if ( $stlcf_timestamp ) {
            wp_unschedule_event( $stlcf_timestamp, 'stlcf_daily_gdpr_cleanup_event' );
        }
    }
}

// 2. The actual execution callback that deletes old records
add_action( 'stlcf_daily_gdpr_cleanup_event', 'stlcf_execute_gdpr_database_cleanup' );
function stlcf_execute_gdpr_database_cleanup() {
    $stlcf_options = get_option( 'stlcf_general_settings', array() );
    $stlcf_days    = isset( $stlcf_options['gdpr_retention_days'] ) ? intval( $stlcf_options['gdpr_retention_days'] ) : 30;
    
    // Safety fallback
    if ( $stlcf_days < 1 ) { $stlcf_days = 30; } 

    global $wpdb;
    $stlcf_entries_table = $wpdb->prefix . 'stlcf_entries';

    // Safely delete leads older than X days
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$stlcf_entries_table} WHERE submitted_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
        $stlcf_days
    ) );
}

stlcf_run_plugin();

