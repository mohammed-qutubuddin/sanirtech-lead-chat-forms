<?php
/**
 * Plugin Name:       SanirTech Lead Chat Forms
 * Description:       Secure, high-converting lead capture form builder with direct chat routing.
 * Version:           1.0.2
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

define( 'STLCF_VERSION', '1.0.2' );
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
    $stlcf_entries_table = esc_sql( $wpdb->prefix . 'stlcf_entries' );

    // Safely delete leads older than X days
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$stlcf_entries_table} WHERE submitted_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
        $stlcf_days
    ) );
    // phpcs:enable
}

// ==========================================================================
// AUTOMATED WEEKLY PERFORMANCE DIGEST CRON
// ==========================================================================

add_filter( 'cron_schedules', 'stlcf_add_weekly_cron_schedule' );
function stlcf_add_weekly_cron_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 7 * DAY_IN_SECONDS,
        'display'  => __( 'Once Weekly', 'sanirtech-lead-chat-forms' )
    );
    return $schedules;
}

add_action( 'init', 'stlcf_setup_weekly_digest_cron' );
function stlcf_setup_weekly_digest_cron() {
    if ( ! wp_next_scheduled( 'stlcf_weekly_digest_cron' ) ) {
        wp_schedule_event( time(), 'weekly', 'stlcf_weekly_digest_cron' );
    }
}

add_action( 'stlcf_weekly_digest_cron', 'stlcf_execute_weekly_performance_report' );
function stlcf_execute_weekly_performance_report() {
    $stlcf_g_settings = get_option( 'stlcf_general_settings', array() );
    $receiver = isset( $stlcf_g_settings['admin_email_receiver'] ) ? $stlcf_g_settings['admin_email_receiver'] : get_option( 'admin_email' );
    if ( empty( $receiver ) ) {
        return;
    }

    global $wpdb;
    $stlcf_table_entries = esc_sql( $wpdb->prefix . 'stlcf_entries' );
    $stlcf_table_forms   = esc_sql( $wpdb->prefix . 'stlcf_forms' );

    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $total_leads = $wpdb->get_var( "SELECT COUNT(id) FROM {$stlcf_table_entries} WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)" );
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $forms_stats = $wpdb->get_results( "SELECT title, views, conversions FROM {$stlcf_table_forms} WHERE status = 'active' ORDER BY conversions DESC LIMIT 5" );
    // phpcs:enable

    $subject = sprintf( '[%s] Direct WhatsApp Lead Forms - Weekly Digest Report', get_bloginfo( 'name' ) );
    
    $body = '<h2>WhatsApp Lead Forms Digest Report</h2>';
    $body .= '<p>Here is your weekly performance summary:</p>';
    $body .= '<p><strong>Total Leads Captured (Last 7 Days):</strong> ' . intval( $total_leads ) . '</p>';
    $body .= '<h3>Top Performing Active Forms</h3>';
    if ( ! empty( $forms_stats ) ) {
        $body .= '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; min-width:350px;">';
        $body .= '<thead><tr style="background:#f8fafc;"><th>Form Title</th><th>Views</th><th>Conversions</th><th>CR%</th></tr></thead>';
        $body .= '<tbody>';
        foreach ( $forms_stats as $fs ) {
            $cr = $fs->views > 0 ? round( ($fs->conversions / $fs->views) * 100, 1 ) : 0;
            $body .= sprintf( '<tr><td>%s</td><td>%d</td><td>%d</td><td>%s%%</td></tr>', esc_html( $fs->title ), intval( $fs->views ), intval( $fs->conversions ), $cr );
        }
        $body .= '</tbody></table>';
    } else {
        $body .= '<p>No active form performance data recorded yet.</p>';
    }
    $body .= '<br><p>Thank you for using Direct Lead Chat Forms!</p>';

    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    wp_mail( $receiver, $subject, $body, $headers );
}

// ==========================================================================
// CRM FAILOVER RETRY CRON
// ==========================================================================

add_filter( 'cron_schedules', 'stlcf_add_30_mins_cron_schedule' );
function stlcf_add_30_mins_cron_schedule( $schedules ) {
    $schedules['thirty_minutes'] = array(
        'interval' => 30 * MINUTE_IN_SECONDS,
        'display'  => __( 'Every 30 Minutes', 'sanirtech-lead-chat-forms' )
    );
    return $schedules;
}

add_action( 'init', 'stlcf_setup_crm_retry_cron' );
function stlcf_setup_crm_retry_cron() {
    if ( ! wp_next_scheduled( 'stlcf_crm_sync_retry_event' ) ) {
        wp_schedule_event( time(), 'thirty_minutes', 'stlcf_crm_sync_retry_event' );
    }
}

add_action( 'stlcf_crm_sync_retry_event', 'stlcf_execute_crm_retry_syncs' );
function stlcf_execute_crm_retry_syncs() {
    $queue = get_option( 'stlcf_failed_crm_syncs_queue', array() );
    if ( empty( $queue ) || ! is_array( $queue ) ) {
        return;
    }

    $remaining_queue = array();
    foreach ( $queue as $item ) {
        $response = wp_remote_post( $item['url'], array(
            'method'  => 'POST',
            'timeout' => 10,
            'headers' => $item['headers'],
            'body'    => is_array( $item['payload'] ) ? wp_json_encode( $item['payload'] ) : $item['payload']
        ));
        
        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 400 ) {
            if ( time() - $item['added_at'] < 7 * DAY_IN_SECONDS ) {
                $remaining_queue[] = $item;
            }
        }
    }
    update_option( 'stlcf_failed_crm_syncs_queue', $remaining_queue );
}

stlcf_run_plugin();

