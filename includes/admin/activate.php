<?php
function rc_api_nasdaq_activate_plugin() {
    if(version_compare(get_bloginfo('version'), '4.2', '<')){
        wp_die(__('You must update WordPress to use this plugin.', 'rc-api-nasdaq'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'rc_api_nasdaq';
    
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `database_code` varchar(40) NOT NULL,
            `dataset_code` varchar(40) NOT NULL,
            `return_format` varchar(40) NOT NULL,
            `api_key` varchar(255) NULL,
            `start_date` date DEFAULT '0000-00-00' NULL,
            `end_date` date DEFAULT '0000-00-00' NULL,
            `order` varchar(40) NOT NULL,
            PRIMARY KEY  (`id`)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $wpdb->insert(
            $table_name,
            array(
                'database_code' => 'WIKI',
                'dataset_code' => 'AAPL',
                'return_format' => 'json',
                'api_key' => '',
                'start_date' => '',
                'end_date' => '',
                'order' => ''
            )
        );
    }
}

function rc_api_nasdaq_options_page() {
    add_menu_page(
        'RC API Nasdaq',
        'RC API Nasdaq',
        'manage_options',
        'rc-api-nasdaq',
        'rc_api_nasdaq_admin_page_html',
        'dashicons-chart-bar',
        20
    );
}
