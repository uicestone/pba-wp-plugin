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
Text Domain: jddj
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'JDDJ_VERSION', '1.0.0' );
define( 'JDDJ__MINIMUM_WP_VERSION', '4.8' );
define( 'JDDJ__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'JDDJ', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'JDDJ', 'plugin_deactivation' ) );

require_once( JDDJ__PLUGIN_DIR . 'class.jddj.php' );
require_once( JDDJ__PLUGIN_DIR . 'class.jddj-rest-api.php' );

add_action( 'rest_api_init', array( 'JDDJ_REST_API', 'init' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( JDDJ__PLUGIN_DIR . 'class.jddj-admin.php' );
	add_action( 'init', array( 'JDDJ_Admin', 'init' ) );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once( JDDJ__PLUGIN_DIR . 'class.jddj-cli.php' );
}
