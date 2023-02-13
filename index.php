<?php
/**
 * Plugin Name:       API Nasdaq - by Rafael Couto
 * Description:       O mesmo busca na API da Nasdaq um ativo e mostra na tela os dados do mesmo de acordo com a base de dados e empresa escolhida.
 * Version:           1.0.8
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rafael Couto
 * Author URI:        http://rafaelscouto.com.br
 * Text Domain:       rcapinasdaq
 */

if(!function_exists('add_action')){
    echo __('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'rcapinasdaq');
    exit;
}

include('includes/admin/activate.php');
include('includes/shortcode/index.php');
include('includes/admin/view.php');
include('includes/rc_api_nasdaq_data_ajax.php');

register_activation_hook(__FILE__, 'rc_api_nasdaq_activate_plugin');
add_action('admin_menu', 'rc_api_nasdaq_options_page');
add_action('init', 'rc_api_nasdaq_shortcodes_init');
add_action('wp_ajax_rc_api_nasdaq_data', 'rc_api_nasdaq_data');
add_action('wp_ajax_nopriv_rc_api_nasdaq_data', 'rc_api_nasdaq_data');

add_shortcode('api_nasdaq', 'rc_api_nasdaq_shortcode_handler');

function rc_api_nasdaq_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=rc-api-nasdaq">' . __( 'Settings' ) . '</a>';
    array_push($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'rc_api_nasdaq_add_settings_link');