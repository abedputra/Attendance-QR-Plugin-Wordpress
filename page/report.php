<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TableAttendance')) {
    require_once AWQC_PLUGIN_DIR . 'class/TableAttendance.php';
}

global $wpdb;

// When click download csv will export data to csv file
if (isset($_GET['action']) && ($_GET['action'] === 'download_csv' || $_GET['action'] === 'download_xlsx')) {

    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'download_attendance_report')) {
        wp_die(esc_html__('Security check failed', 'attendance_with_qr_code'));
    }

    // Sanitize input
    $awqc_user_search_key = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['s'] ?? ''))) : '';
    $awqc_date_from = isset($_REQUEST['date_from']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['date_from'] ?? ''))) : '';
    $awqc_date_to = isset($_REQUEST['date_to']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['date_to'] ?? ''))) : '';

    // Generate file name
    $awqc_filename = 'data_attendance_' . time();
    // Add header
    $awqc_header_row = array(
        'Name',
        'Date',
        'In Time',
        'Out Time',
        'Work Hour',
        'Over Time',
        'Late Time',
        'Early Out Time',
        'In Location',
        'Out Location'
    );

    $awqc_data_rows = array();
    $awqc_table_name = $wpdb->prefix . 'attendances';

    // Build query with prepared statements
    $awqc_where_clauses = array('1=1');
    $awqc_where_values = array();

    // Check search name
    if ($awqc_user_search_key !== '') {
        $awqc_where_clauses[] = 'name LIKE %s';
        $awqc_where_values[] = '%' . $wpdb->esc_like($awqc_user_search_key) . '%';
    }

    // Check date
    if ($awqc_date_from !== '' && $awqc_date_to !== '') {
        $awqc_where_clauses[] = '`date` BETWEEN %s AND %s';
        $awqc_where_values[] = $awqc_date_from;
        $awqc_where_values[] = $awqc_date_to;
    }

    $awqc_where_sql = implode(' AND ', $awqc_where_clauses);
    $awqc_table_name_escaped = esc_sql($awqc_table_name);

    // Build query - table name is already escaped with esc_sql()
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
    $awqc_sql = "SELECT * FROM {$awqc_table_name_escaped} WHERE $awqc_where_sql ORDER BY `date` DESC, `in_time` DESC";

    if (!empty($awqc_where_values)) {
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $awqc_sql = $wpdb->prepare($awqc_sql, $awqc_where_values);
    }

    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $awqc_get_data = $wpdb->get_results($awqc_sql, 'ARRAY_A');

    foreach ($awqc_get_data as $awqc_data) {
        $awqc_row = array(
            isset($awqc_data['name']) ? $awqc_data['name'] : '',
            isset($awqc_data['date']) ? $awqc_data['date'] : '',
            isset($awqc_data['in_time']) ? $awqc_data['in_time'] : '',
            isset($awqc_data['out_time']) ? $awqc_data['out_time'] : '',
            isset($awqc_data['work_hour']) ? $awqc_data['work_hour'] : '',
            isset($awqc_data['over_time']) ? $awqc_data['over_time'] : '',
            isset($awqc_data['late_time']) ? $awqc_data['late_time'] : '',
            isset($awqc_data['early_out_time']) ? $awqc_data['early_out_time'] : '',
            isset($awqc_data['in_location']) ? $awqc_data['in_location'] : '',
            isset($awqc_data['out_location']) ? $awqc_data['out_location'] : ''
        );
        $awqc_data_rows[] = $awqc_row;
    }

    // Clean any existing output buffers before sending file
    while (ob_get_level()) {
        ob_end_clean();
    }

    $awqc_file_handle = @fopen('php://output', 'w');
    if (!$awqc_file_handle) {
        wp_die(esc_html__('Unable to create file for download.', 'attendance_with_qr_code'));
    }

    header('Content-Type: application/octet-stream'); // tells browser to download
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');

    // Check user choose csv or xlsx
    if (isset($_GET['action']) && sanitize_text_field(wp_unslash($_GET['action'])) === 'download_csv') {
        header('Content-type: text/csv');
        $awqc_filename .= '.csv';
        header('Content-Disposition: attachment; filename=' . esc_attr($awqc_filename));
        fputcsv($awqc_file_handle, $awqc_header_row);
        foreach ($awqc_data_rows as $awqc_data_row) {
            fputcsv($awqc_file_handle, $awqc_data_row);
        }
    } else {
        // XLSX format - use CSV format as fallback since XLSX requires additional library
        // For proper XLSX support, consider using PhpSpreadsheet library
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $awqc_filename .= '.xlsx';
        header('Content-Disposition: attachment; filename=' . esc_attr($awqc_filename));
        // Write data as CSV format (Excel can open CSV files)
        // Note: This is a simplified implementation. For true XLSX format, use PhpSpreadsheet library.
        fputcsv($awqc_file_handle, $awqc_header_row);
        foreach ($awqc_data_rows as $awqc_data_row) {
            fputcsv($awqc_file_handle, $awqc_data_row);
        }
    }

    header('Expires: 0');
    header('Pragma: public');
    // Direct fclose() is required for php://output stream used in file downloads.
    fclose($awqc_file_handle); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
    exit();
}

$awqc_table = new TableAttendance();
$awqc_table->prepare_items();
$awqc_user_search_key_param = isset($_REQUEST['s']) ? '&s=' . urlencode(sanitize_text_field(wp_unslash(trim($_REQUEST['s'] ?? '')))) : '';

if ('delete' === $awqc_table->current_action()) {
    $awqc_message = esc_html__('Items deleted successfully!', 'attendance_with_qr_code');
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Attendance Report', 'attendance_with_qr_code'); ?></h1>
    <hr class="wp-header-end">

    <?php if (!empty($awqc_message)) : ?>
        <div class="notice notice-success is-dismissible" style="margin: 20px 0;">
            <p><?php echo esc_html($awqc_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="notice notice-info" style="margin: 20px 0;">
        <p><strong><?php echo esc_html__('Attendance Report', 'attendance_with_qr_code'); ?></strong> <?php echo esc_html__('View and manage employee attendance records. You can search by name and filter by date range, then export the data to CSV or XLSX format.', 'attendance_with_qr_code'); ?></p>
    </div>

    <div class="awqc-search-form">
        <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="awqc-attendance-menu">
            <?php wp_nonce_field('download_attendance_report'); ?>
            <div class="search-box">
                <label for="search-name" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <?php echo esc_html__('Search', 'attendance_with_qr_code'); ?>:
                </label>
                <input type="text" id="search-name" name="s" placeholder="<?php esc_attr_e('Name', 'attendance_with_qr_code'); ?>" autocomplete="off" value="<?php echo isset($_REQUEST['s']) ? esc_attr(sanitize_text_field(wp_unslash($_REQUEST['s']))) : ''; ?>">
                <input type="date" id="date_from" name="date_from" value="<?php echo isset($_REQUEST['date_from']) ? esc_attr(sanitize_text_field(wp_unslash($_REQUEST['date_from']))) : ''; ?>">
                <input type="date" id="date_to" name="date_to" value="<?php echo isset($_REQUEST['date_to']) ? esc_attr(sanitize_text_field(wp_unslash($_REQUEST['date_to']))) : ''; ?>">
                <input type="submit" id="search-submit" class="button button-primary" value="<?php esc_attr_e('Search', 'attendance_with_qr_code'); ?>">
                <?php if (isset($_REQUEST['s']) || isset($_REQUEST['date_from']) || isset($_REQUEST['date_to'])) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=awqc-attendance-menu')); ?>" class="button"><?php echo esc_html__('Clear', 'attendance_with_qr_code'); ?></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div style="margin: 15px 0;">
        <?php
        $awqc_date_from = isset($_REQUEST['date_from']) ? sanitize_text_field(wp_unslash($_REQUEST['date_from'])) : '';
        $awqc_date_to = isset($_REQUEST['date_to']) ? sanitize_text_field(wp_unslash($_REQUEST['date_to'])) : '';
        ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=awqc-attendance-menu&action=download_csv' . $awqc_user_search_key_param . '&noheader=1&date_from=' . urlencode($awqc_date_from) . '&date_to=' . urlencode($awqc_date_to) . '&_wpnonce=' . wp_create_nonce('download_attendance_report'))); ?>" class="button">
            <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> <?php echo esc_html__('Export to CSV', 'attendance_with_qr_code'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=awqc-attendance-menu&action=download_xlsx' . $awqc_user_search_key_param . '&noheader=1&date_from=' . urlencode($awqc_date_from) . '&date_to=' . urlencode($awqc_date_to) . '&_wpnonce=' . wp_create_nonce('download_attendance_report'))); ?>" class="button">
            <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> <?php echo esc_html__('Export to XLSX', 'attendance_with_qr_code'); ?>
        </a>
    </div>

    <form id="attendances-table" method="GET">
        <input type="hidden" name="page" value="<?php echo esc_attr(isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : ''); ?>" />
        <?php
        $awqc_table->display();
        ?>
    </form>
</div>