<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('TableHistoryQr')) {
    require_once AWQC_PLUGIN_DIR . 'class/TableHistoryQr.php';
}

global $wpdb;
$awqc_message = '';

// When click import will import from users DB
if (isset($_GET['action']) && $_GET['action'] === 'import' && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'import_users')) {
    $awqc_table_user = $wpdb->prefix . 'users';
    $awqc_meta = $wpdb->prefix . 'usermeta';

    $awqc_table_user_escaped = esc_sql($awqc_table_user);
    $awqc_meta_escaped = esc_sql($awqc_meta);
    
    // Table names are already escaped with esc_sql()
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
    $awqc_sql_get_data = $wpdb->prepare(
        "SELECT a.user_email, b1.meta_value AS 'first_name', b2.meta_value as 'last_name'
         FROM {$awqc_table_user_escaped} a
         INNER JOIN {$awqc_meta_escaped} b1 ON b1.user_id = a.ID AND b1.meta_key = %s
         INNER JOIN {$awqc_meta_escaped} b2 ON b2.user_id = a.ID AND b2.meta_key = %s",
        'first_name',
        'last_name'
    );

    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $awqc_results = $wpdb->get_results($awqc_sql_get_data);

    if (!empty($awqc_results)) {
        $awqc_count_data = 0;
        $awqc_error_count = 0;

        foreach ($awqc_results as $awqc_row) {
            // Data from database is already safe, no need for esc_html here
            $awqc_first_name = isset($awqc_row->first_name) ? trim($awqc_row->first_name) : '';
            $awqc_last_name = isset($awqc_row->last_name) ? trim($awqc_row->last_name) : '';
            $awqc_full_name = trim($awqc_first_name . ' ' . $awqc_last_name);

            // Check if data name of users is empty, show message error
            // and don't import it.
            if (empty($awqc_first_name) && empty($awqc_last_name)) {
                $awqc_error_count++;
            } else {
                // Sanitize full name before using in query
                $awqc_full_name_sanitized = sanitize_text_field($awqc_full_name);
                if (empty($awqc_full_name_sanitized)) {
                    $awqc_error_count++;
                    continue;
                }
                
                $awqc_table_history = $wpdb->prefix . 'history_qr';
                $awqc_table_history_escaped = esc_sql($awqc_table_history);
                
                // Table name is already escaped with esc_sql()
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
                $awqc_check_sql = $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$awqc_table_history_escaped} WHERE `name` = %s",
                    $awqc_full_name_sanitized
                );

                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                if ($wpdb->get_var($awqc_check_sql) == 0) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
                    $wpdb->insert($awqc_table_history, array(
                        'name' => $awqc_full_name_sanitized,
                    ), array('%s'));

                    $awqc_count_data++;
                }
            }
        }

        if ($awqc_count_data > 0) {
            /* translators: %d: number of imported items */
            $awqc_message = sprintf(esc_html__('Successfully imported %d data.', 'attendance_with_qr_code'), $awqc_count_data);
        } else {
            $awqc_message = esc_html__('No data is imported, maybe because there is duplicate data.', 'attendance_with_qr_code');
        }

        if ($awqc_error_count > 0) {
            /* translators: %d: number of items without names */
            $awqc_message .= ' ' . sprintf(esc_html__('However, %d data doesn\'t have a name, and we didn\'t import it.', 'attendance_with_qr_code'), $awqc_error_count);
        }
    } else {
        $awqc_message = esc_html__('No data is imported.', 'attendance_with_qr_code');
    }
}

$awqc_table = new TableHistoryQr();
$awqc_table->prepare_items();

if ('delete' === $awqc_table->current_action()) {
    $awqc_message = esc_html__('Items deleted successfully!', 'attendance_with_qr_code');
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('QR Code History', 'attendance_with_qr_code'); ?></h1>
    
    <hr class="wp-header-end">
    
    <?php if (!empty($awqc_message)) : ?>
        <div class="notice notice-success is-dismissible" style="margin: 20px 0;">
            <p><?php echo wp_kses_post($awqc_message); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="notice notice-info" style="margin: 20px 0;">
        <p><strong><?php echo esc_html__('QR Code History', 'attendance_with_qr_code'); ?></strong> <?php echo esc_html__('View and manage all generated QR codes. You can import names from WordPress users or manually add them. Each name can be used to generate a unique QR code for attendance tracking.', 'attendance_with_qr_code'); ?></p>
    </div>

    <div style="margin: 20px 0;">
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=awqc-history-qr&action=import'), 'import_users')); ?>" class="button button-primary">
            <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> <?php echo esc_html__('Import Names From WordPress Users', 'attendance_with_qr_code'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=awqc-generate-qr')); ?>" class="button">
            <span class="dashicons dashicons-plus" style="margin-top: 3px;"></span> <?php echo esc_html__('Generate New QR Code', 'attendance_with_qr_code'); ?>
        </a>
    </div>

    <form id="persons-table" method="GET">
        <?php wp_nonce_field('bulk-' . $awqc_table->_args['plural']); ?>
        <input type="hidden" name="page" value="<?php echo esc_attr(isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : ''); ?>" />
        <?php
        $awqc_table->search_box(esc_html__('Search', 'attendance_with_qr_code'), 'search');
        $awqc_table->display();
        ?>
    </form>
</div>