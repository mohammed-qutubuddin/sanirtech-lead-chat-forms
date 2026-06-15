<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$stlcf_entries_table = $wpdb->prefix . 'stlcf_entries';

// 1. Data Aggregation: Fetch Core Metrics
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
$stlcf_total_leads = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$stlcf_entries_table}" );
$stlcf_leads_7_days = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$stlcf_entries_table} WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)" );
$stlcf_top_form_id = $wpdb->get_var( "SELECT form_id FROM {$stlcf_entries_table} GROUP BY form_id ORDER BY COUNT(id) DESC LIMIT 1" );
// phpcs:enable

$stlcf_top_form_name = __( 'N/A', 'sanirtech-lead-chat-forms' );
if ( $stlcf_top_form_id > 0 ) {
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $stlcf_top_form_name = $wpdb->get_var( $wpdb->prepare( "SELECT title FROM {$wpdb->prefix}stlcf_forms WHERE id = %d", $stlcf_top_form_id ) );
    // phpcs:enable
}

// 2. Data Aggregation: Chart Matrix (Last 7 Days Processing)
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
$stlcf_daily_data = $wpdb->get_results( "SELECT DATE(submitted_at) as submit_date, COUNT(id) as lead_count FROM {$stlcf_entries_table} WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(submitted_at) ORDER BY submit_date ASC" );
// phpcs:enable

$stlcf_chart_labels = array();
$stlcf_chart_counts = array();

// Loop through exactly 7 days to prevent empty gaps in the chart
for ( $i = 6; $i >= 0; $i-- ) {
    $stlcf_date_str = gmdate( 'Y-m-d', strtotime( "-$i days" ) );
    $stlcf_chart_labels[] = gmdate( 'M j', strtotime( $stlcf_date_str ) );
    
    $stlcf_found_count = 0;
    if ( is_array( $stlcf_daily_data ) ) {
        foreach ( $stlcf_daily_data as $stlcf_row ) {
            if ( $stlcf_row->submit_date === $stlcf_date_str ) {
                $stlcf_found_count = (int) $stlcf_row->lead_count;
                break;
            }
        }
    }
    $stlcf_chart_counts[] = $stlcf_found_count;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Conversion Analytics', 'sanirtech-lead-chat-forms' ); ?></h1>
    <hr class="wp-header-end">

    <div style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
        
        <div class="stlcf-card" style="flex: 1; min-width: 250px; margin-top: 0;">
            <div class="stlcf-card-body" style="text-align: center; padding: 30px 20px;">
                <h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php esc_html_e( 'Total Leads (All Time)', 'sanirtech-lead-chat-forms' ); ?></h3>
                <div style="font-size: 42px; font-weight: 700; color: #0f172a; margin-top: 10px;"><?php echo esc_html( $stlcf_total_leads ); ?></div>
            </div>
        </div>

        <div class="stlcf-card" style="flex: 1; min-width: 250px; margin-top: 0;">
            <div class="stlcf-card-body" style="text-align: center; padding: 30px 20px;">
                <h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php esc_html_e( 'Leads (Last 7 Days)', 'sanirtech-lead-chat-forms' ); ?></h3>
                <div style="font-size: 42px; font-weight: 700; color: #2563eb; margin-top: 10px;"><?php echo esc_html( $stlcf_leads_7_days ); ?></div>
            </div>
        </div>

        <div class="stlcf-card" style="flex: 1; min-width: 250px; margin-top: 0;">
            <div class="stlcf-card-body" style="text-align: center; padding: 30px 20px;">
                <h3 style="margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php esc_html_e( 'Top Performing Form', 'sanirtech-lead-chat-forms' ); ?></h3>
                <div style="font-size: 24px; font-weight: 600; color: #10b981; margin-top: 22px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html( $stlcf_top_form_name ); ?></div>
            </div>
        </div>

    </div>

    <div class="stlcf-card" style="margin-top: 10px;">
        <div class="stlcf-card-header">
            <h2><?php esc_html_e( 'Lead Generation Timeline (Last 7 Days)', 'sanirtech-lead-chat-forms' ); ?></h2>
        </div>
        <div class="stlcf-card-body" style="position: relative; height: 400px; width: 100%;">
            <canvas id="stlcfLeadsChart"></canvas>
        </div>
    </div>
</div>

<script>
// Prevent execution until Chart.js is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('stlcfLeadsChart').getContext('2d');
    
    // Create a premium gradient fill for the line chart
    var gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
    gradientFill.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
    gradientFill.addColorStop(1, 'rgba(37, 99, 235, 0)');

    var leadsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo wp_json_encode( $stlcf_chart_labels ); ?>,
            datasets: [{
                label: '<?php esc_html_e( 'Form Submissions', 'sanirtech-lead-chat-forms' ); ?>',
                data: <?php echo wp_json_encode( $stlcf_chart_counts ); ?>,
                borderColor: '#2563eb',
                backgroundColor: gradientFill,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Creates smooth, curved lines instead of sharp angles
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13, family: 'sans-serif' },
                    bodyFont: { size: 14, weight: 'bold', family: 'sans-serif' },
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1, color: '#64748b' },
                    grid: { color: '#f1f5f9', drawBorder: false }
                },
                x: {
                    ticks: { color: '#64748b' },
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
});
</script>