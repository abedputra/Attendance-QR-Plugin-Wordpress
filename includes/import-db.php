<?php

defined('ABSPATH') || exit;

/**
 * Create database tables for attendance plugin
 */
function awqc_create_table()
{
    global $wpdb;
    $awqc_plugin_version = '3.0';
    $charset_collate = $wpdb->get_charset_collate();

    $awqc_attendance_table = $wpdb->prefix . 'attendances';
    $awqc_history_qr_table = $wpdb->prefix . 'history_qr';
    $awqc_settings_table = $wpdb->prefix . 'settings';
    $awqc_current_version = get_option('attendance_with_qr_code');

    $awqc_sql = array();

    if ($awqc_current_version != $awqc_plugin_version) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $awqc_attendance_table)) != $awqc_attendance_table) {
            $awqc_sql[] = "CREATE TABLE $awqc_attendance_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(50) NOT NULL,
                date date NOT NULL,
                in_time time NOT NULL,
                out_time time DEFAULT NULL,
                work_hour time DEFAULT NULL,
                over_time time DEFAULT NULL,
                late_time time DEFAULT NULL,
                early_out_time time DEFAULT NULL,
                in_location varchar(200) NOT NULL,
                out_location varchar(200) DEFAULT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $awqc_history_qr_table)) != $awqc_history_qr_table) {
            $awqc_sql[] = "CREATE TABLE $awqc_history_qr_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $awqc_settings_table)) != $awqc_settings_table) {
            $awqc_sql[] = "CREATE TABLE $awqc_settings_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                start_time time NOT NULL,
                out_time time NOT NULL,
                key_insert char(40) NOT NULL,
                timezone varchar(100) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        }

        if (!empty($awqc_sql)) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            foreach ($awqc_sql as $awqc_query) {
                dbDelta($awqc_query);
            }

            $awqc_settings_table_escaped = esc_sql($awqc_settings_table);
            // Table name is already escaped with esc_sql()
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
            $awqc_settings_data = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$awqc_settings_table_escaped} WHERE id = %d LIMIT 1",
                1
            ));
            if (count($awqc_settings_data) == 0) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->insert(
                    $awqc_settings_table,
                    array(
                        'id' => 1,
                        'start_time' => '08:00:00',
                        'out_time' => '17:00:00',
                        'key_insert' => '51e69892ab49df85c6230ccc57f8e1d1606cabbb',
                        'timezone' => 'Asia/Makassar'
                    ),
                    array('%d', '%s', '%s', '%s', '%s')
                );
            }
        }

        update_option('attendance_with_qr_code', $awqc_plugin_version);
    } else {
        add_option('attendance_with_qr_code', $awqc_plugin_version);
    }
    
    // Migration: Drop many_employee column if it exists (for existing installations)
    // This runs every time to ensure the column is removed
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
    $awqc_column_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s 
        AND TABLE_NAME = %s 
        AND COLUMN_NAME = 'many_employee'",
        DB_NAME,
        $awqc_settings_table
    ));
    
    if (!empty($awqc_column_exists)) {
        // For DROP COLUMN, we need to use esc_sql for table name
        $awqc_settings_table_escaped = esc_sql($awqc_settings_table);
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $wpdb->query("ALTER TABLE $awqc_settings_table_escaped DROP COLUMN many_employee");
    }
}

register_activation_hook(AWQC_PLUGIN_FILE, 'awqc_create_table');
