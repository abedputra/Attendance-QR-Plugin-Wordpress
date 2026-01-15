<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * TableHistoryQr class that will display table
 *
 * Note: Class name follows original naming convention for backward compatibility.
 * Extends WP_List_Table which is a WordPress core class.
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class TableHistoryQr extends WP_List_Table
{
    /**
     * [REQUIRED] must declare constructor and give some basic params
     */
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'history',
            'plural'   => 'histories',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param array $item - row (key, value array)
     * @param string $column_name - string (key)
     *
     * @return string HTML
     */
    public function column_default($item, $column_name)
    {
        return esc_html($item[$column_name]);
    }

    /**
     * [OPTIONAL] render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param array $item - row (key, value array)
     *
     * @return string
     */
    public function column_name($item)
    {
        $awqc_qr = json_encode(array('name' => $item['name']));
        // WP_List_Table handles nonce verification internally, $_REQUEST access is safe here
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $awqc_page = isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : '';
        $awqc_delete_url = wp_nonce_url(
            admin_url('admin.php?page=' . urlencode($awqc_page) . '&action=delete&id=' . intval($item['id'])),
            'bulk-' . $this->_args['plural']
        );
        // Use local QR generator with caching for faster loading
        $awqc_qr_save_url = awqc_get_qr_code_url($awqc_qr, 400);
        $actions = array(
            'save'   => sprintf('<a href="%s" target="_blank">%s</a>', esc_url($awqc_qr_save_url), esc_html__('Save', 'attendance_with_qr_code')),
            'delete' => sprintf('<a href="%s">%s</a>', esc_url($awqc_delete_url), esc_html__('Delete', 'attendance_with_qr_code')),
        );

        return sprintf(
            '%s %s',
            esc_html($item['name']),
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] checkbox column renders
     *
     * @param array $item - row (key, value array)
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            esc_attr($item['id'])
        );
    }

    /**
     * Render QR code image column
     *
     * @param array $item - row (key, value array)
     *
     * @return string
     */
    public function column_image_qr($item)
    {
        $awqc_qr = json_encode(array('name' => $item['name']));
        // Use local QR generator with caching for faster loading
        $awqc_qr_url = awqc_get_qr_code_url($awqc_qr, 100);

        return sprintf(
            '<img src="%s" alt="%s">',
            esc_url($awqc_qr_url),
            esc_attr($item['name'])
        );
    }

    /**
     * [OPTIONAL] Return array of bulk actions if has any
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        return array(
            'delete' => esc_html__('Delete', 'attendance_with_qr_code')
        );
    }

    /**
     * [REQUIRED] method prepare our data
     *
     * It will get rows from database and prepare them to be showed in table
     */
    public function prepare_items()
    {
        global $wpdb;
        $awqc_table_name = $wpdb->prefix . 'history_qr'; // do not forget about tables prefix

        $awqc_per_page = 5; // constant, how much records will be shown per page

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // check if a search was performed.
        // WP_List_Table handles nonce verification internally, $_REQUEST access is safe here
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $awqc_user_search_key = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['s']))) : '';

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

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
        $awqc_orderby = (isset($_REQUEST['orderby']) && array_key_exists(sanitize_text_field(wp_unslash($_REQUEST['orderby'])), $this->get_sortable_columns())) ? sanitize_text_field(wp_unslash($_REQUEST['orderby'])) : 'name';
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

        // Build SQL query
        $awqc_where_sql = !empty($awqc_where_clauses) ? 'WHERE ' . implode(' AND ', $awqc_where_clauses) : '';
        
        // Sanitize orderby and order for direct use in ORDER BY
        $awqc_orderby_safe = in_array($awqc_orderby, array('name')) ? $awqc_orderby : 'name';
        $awqc_order_safe = strtoupper($awqc_order) === 'DESC' ? 'DESC' : 'ASC';
        
        // Use esc_sql for column name in ORDER BY
        $awqc_orderby_escaped = esc_sql($awqc_orderby_safe);
        $awqc_order_sql = "ORDER BY `$awqc_orderby_escaped` $awqc_order_safe";
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
            'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
            'image_qr' => esc_html__('QR', 'attendance_with_qr_code'),
            'name'     => esc_html__('Name', 'attendance_with_qr_code'),
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
        return array(
            'name' => array('name', true),
        );
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    public function process_bulk_action()
    {
        global $wpdb;
        $awqc_table_name = $wpdb->prefix . 'history_qr'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            // Verify nonce
            if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'bulk-' . $this->_args['plural'])) {
                return;
            }

            $awqc_ids = isset($_REQUEST['id']) ? array_map('intval', (array) $_REQUEST['id']) : array();
            if (!empty($awqc_ids)) {
                $awqc_ids_clean = array_map('absint', $awqc_ids);
                
                // Use IN clause with proper escaping
                $awqc_table_name_escaped = esc_sql($awqc_table_name);
                
                // Build placeholders for IN clause
                $awqc_ids_placeholders = implode(',', array_fill(0, count($awqc_ids_clean), '%d'));
                
                // Build query - use call_user_func_array for dynamic number of arguments
                // Table name and placeholders are already properly escaped
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
                $awqc_sql = "DELETE FROM {$awqc_table_name_escaped} WHERE id IN($awqc_ids_placeholders)";
                
                // Use call_user_func_array to pass array as arguments to prepare
                $awqc_prepare_args = array_merge(array($awqc_sql), $awqc_ids_clean);
                $awqc_sql_prepared = call_user_func_array(array($wpdb, 'prepare'), $awqc_prepare_args);
                
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $wpdb->query($awqc_sql_prepared);
            }
        }
    }
}
