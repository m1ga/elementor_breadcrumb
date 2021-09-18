
<?php
/**
* Plugin Name: Breadcrumb for Elementor
* Plugin URI: https://www.migaweb.de/
* Description: Simple breadcrumb for Elementor
* Version: 1.0
* Author: Michael Gangolf
* Author URI: https://www.migaweb.de/
**/

add_action('admin_init', 'mec_lite_dp_register_settings');
add_action('admin_menu', 'addMenu');
add_action('init', 'custom_rewrite_rules');
add_action('wp_enqueue_scripts', 'enqueue_style');


use Elementor\Plugin;

add_action('init', static function () {
    require_once(__DIR__ . '/widget/Breadcrumb.php');
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor_Widget_Breadcrumb());
});
