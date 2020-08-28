<?php
/*
Plugin Name: Simple RESTful Post
Plugin URI: https://www.hbird.com.cn/
Description: Provide simple RESTful post api
Version: 1.0.0
Author: Uice Lu
Author URI: https://cecilia.uice.lu
License: GPLv2 or later
Text Domain: pba
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'PBA_VERSION', '1.0.0' );
define( 'PBA__MINIMUM_WP_VERSION', '4.8' );
define( 'PBA__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'PBA', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'PBA', 'plugin_deactivation' ) );

require_once( PBA__PLUGIN_DIR . 'class.pba.php' );
require_once( PBA__PLUGIN_DIR . 'class.pba-rest-api.php' );

add_action( 'rest_api_init', array( 'PBA_REST_API', 'init' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( PBA__PLUGIN_DIR . 'class.pba-admin.php' );
	add_action( 'init', array( 'PBA_Admin', 'init' ) );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once( PBA__PLUGIN_DIR . 'class.pba-cli.php' );
}
