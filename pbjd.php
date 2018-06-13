<?php
/**
 * @package Akismet
 */
/*
Plugin Name: Jiading CPC Center server
Plugin URI: https://jddj.hbird.com.cn/
Description: Server end restful apis for Jiading CPC Center
Version: 1.0.0
Author: Uice Lu
Author URI: https://cecilia.uice.lu
License: GPLv2 or later
Text Domain: pbjd
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'PBJD_VERSION', '1.0.0' );
define( 'PBJD__MINIMUM_WP_VERSION', '4.8' );
define( 'PBJD__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'PBJD', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'PBJD', 'plugin_deactivation' ) );

require_once( PBJD__PLUGIN_DIR . 'class.pbjd.php' );
require_once( PBJD__PLUGIN_DIR . 'class.pbjd-rest-api.php' );

add_action( 'rest_api_init', array( 'PBJD_REST_API', 'init' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( PBJD__PLUGIN_DIR . 'class.pbjd-admin.php' );
	add_action( 'init', array( 'PBJD_Admin', 'init' ) );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once( PBJD__PLUGIN_DIR . 'class.pbjd-cli.php' );
}
