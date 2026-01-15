<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * TableAttendance class that will display table
 * for attendance report list
 *
 * Note: Class name follows original naming convention for backward compatibility.
 * Extends WP_List_Table which is a WordPress core class.
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class TableAttendance extends WP_List_Table
{
    /**
     * [REQUIRED] must declare constructor and give some basic params
     */
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'attendance',
            'plural'   => 'attendances',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param array $item - row (key, value array)
     * @param string $column_name - string (key)
     *
     * @return string
     */
    public function column_default($item, $column_name)
    {
        return esc_html($item[$column_name]);
    }

    /**
     * [OPTIONAL] date change text 'today'
     *
     * @param array $item - row (key, value array)
     *
     * @return string
     */
    public function column_date($item)
    {
        $today = current_time('Y-m-d');
        if ($today === $item['date']) {
            $showDate = '<span class="red-text">' . esc_html__('Today', 'attendance_with_qr_code') . '</span>';
        } else {
            $showDate = esc_html($item['date']);
        }

        return $showDate;
    }

    /**
     * [REQUIRED] method prepare our data
     *
     * It will get rows from database and prepare them to be showed in table
     */
    public function prepare_items()
    {
        global $wpdb;
        $awqc_table_name = $wpdb->prefix . 'attendances'; // do not forget about tables prefix

        // check if a search was performed.
        // WP_List_Table handles nonce verification internally, $_REQUEST access is safe here
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $awqc_user_search_key = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['s']))) : '';

        $awqc_per_page = 5; // constant, how much records will be shown per page

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // will be used in pagination settings
        $awqc_table_name_escaped = esc_sql($awqc_table_name);
        // Table name is already escaped with esc_sql()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
        $awqc_total_items = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM {$awqc_table_name_escaped}"
        ));

        // prepare query params, as usual current page, order by and order direction
        // WP_List_Table handles nonce verification internally
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $awqc_paged   = isset($_REQUEST['paged']) ? ($awqc_per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $awqc_orderby = (isset($_REQUEST['orderby']) && array_key_exists(sanitize_text_field(wp_unslash($_REQUEST['orderby'])), $this->get_sortable_columns())) ? sanitize_sql_orderby(sanitize_text_field(wp_unslash($_REQUEST['orderby']))) : 'name';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $awqc_order   = (isset($_REQUEST['order']) && in_array(sanitize_text_field(wp_unslash($_REQUEST['order'])), array('asc', 'desc'))) ? sanitize_text_field(wp_unslash($_REQUEST['order'])) : 'asc';

        // Build WHERE clause
        $awqc_where_clauses = array();
        $awqc_where_values = array();

        // Check search name
        if ($awqc_user_search_key !== '') {
            $awqc_where_clauses[] = 'name LIKE %s';
            $awqc_where_values[] = '%' . $wpdb->esc_like($awqc_user_search_key) . '%';
        }

        // Check date
        // WP_List_Table handles nonce verification internally, $_REQUEST access is safe here
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_REQUEST['date_from'], $_REQUEST['date_to']) && $_REQUEST['date_from'] !== '' && $_REQUEST['date_to'] !== '') {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $awqc_date_from = sanitize_text_field(wp_unslash($_REQUEST['date_from']));
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $awqc_date_to = sanitize_text_field(wp_unslash($_REQUEST['date_to']));
            $awqc_where_clauses[] = '`date` BETWEEN %s AND %s';
            $awqc_where_values[] = $awqc_date_from;
            $awqc_where_values[] = $awqc_date_to;
        }

        // Build SQL query
        $awqc_where_sql = !empty($awqc_where_clauses) ? 'WHERE ' . implode(' AND ', $awqc_where_clauses) : '';
        
        // Sanitize orderby and order for direct use in ORDER BY
        $awqc_allowed_orderby = array('name', 'date', 'in_time', 'out_time', 'work_hour', 'over_time', 'late_time', 'early_out_time', 'in_location', 'out_location');
        $awqc_orderby_safe = in_array($awqc_orderby, $awqc_allowed_orderby) ? esc_sql($awqc_orderby) : 'name';
        $awqc_order_safe = strtoupper($awqc_order) === 'DESC' ? 'DESC' : 'ASC';
        
        // Build ORDER BY clause
        if (empty($awqc_where_clauses) && !isset($_REQUEST['orderby'])) {
            $awqc_order_sql = 'ORDER BY `date` DESC, `in_time` DESC';
        } else {
            // Use esc_sql for column name in ORDER BY
            $awqc_orderby_escaped = esc_sql($awqc_orderby_safe);
            $awqc_order_sql = "ORDER BY `$awqc_orderby_escaped` $awqc_order_safe";
        }
        
        $awqc_table_name_escaped = esc_sql($awqc_table_name);
        // Build query - table name and order by are already escaped with esc_sql()
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
        $awqc_sql = "SELECT * FROM {$awqc_table_name_escaped} $awqc_where_sql $awqc_order_sql LIMIT %d OFFSET %d";
        $awqc_prepare_values = array_merge($awqc_where_values, array($awqc_per_page, $awqc_paged));

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared
        $this->items = $wpdb->get_results($wpdb->prepare($awqc_sql, $awqc_prepare_values), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $awqc_total_items, // total items defined above
            'per_page'    => $awqc_per_page, // per page constant defined at top of method
            'total_pages' => ceil($awqc_total_items / $awqc_per_page) // calculate pages count
        ));
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'name'           => esc_html__('Name', 'attendance_with_qr_code'),
            'date'           => esc_html__('Date', 'attendance_with_qr_code'),
            'in_time'        => esc_html__('In Time', 'attendance_with_qr_code'),
            'out_time'       => esc_html__('Out Time', 'attendance_with_qr_code'),
            'work_hour'      => esc_html__('Work Hour', 'attendance_with_qr_code'),
            'over_time'      => esc_html__('Over Time', 'attendance_with_qr_code'),
            'late_time'      => esc_html__('Late Time', 'attendance_with_qr_code'),
            'early_out_time' => esc_html__('Early Out Time', 'attendance_with_qr_code'),
            'in_location'    => esc_html__('In Location', 'attendance_with_qr_code'),
            'out_location'   => esc_html__('Out Location', 'attendance_with_qr_code'),
        );

        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name'           => array('name', true),
            'date'           => array('date', true),
            'in_time'        => array('in_time', true),
            'out_time'       => array('out_time', true),
            'work_hour'      => array('work_hour', true),
            'over_time'      => array('over_time', true),
            'late_time'      => array('late_time', true),
            'early_out_time' => array('early_out_time', true),
            'in_location'    => array('in_location', false),
            'out_location'   => array('out_location', false),
        );

        return $sortable_columns;
    }
}
