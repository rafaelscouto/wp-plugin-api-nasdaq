<?php
function rc_api_nasdaq_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rc_api_nasdaq';

    $api_key = $_GET['api_key'];
    $database_code = strtoupper($_GET['database_code']);
    $dataset_code = strtoupper($_GET['dataset_code']);
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $url = "https://data.nasdaq.com/api/v3/datasets/$database_code/$dataset_code.json?api_key=$api_key&start_date=$start_date&end_date=$end_date&rows=1";

    $response = wp_remote_get($url);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE dataset_code = '$dataset_code' AND database_code = '$database_code'");

    if (empty($results)) {
        $wpdb->insert(
            $table_name,
            array(
                'api_key' => $api_key,
                'return_format' => 'json',
                'database_code' => $database_code,
                'dataset_code' => $dataset_code,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order' => 'asc'
            )
        );
    } else {
        $wpdb->update(
            $table_name,
            array(
                'api_key' => $api_key,
                'return_format' => 'json',
                'database_code' => $database_code,
                'dataset_code' => $dataset_code,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'order' => 'asc',
            ),
            array(
                'dataset_code' => $dataset_code,
                'database_code' => $database_code,
            )
        );
    }
    wp_send_json_success($data);
}