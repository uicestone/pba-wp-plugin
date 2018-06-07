<?php

require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-posts-controller.php' );
require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-speeches-controller.php' );

class JDDJ_REST_API {

	public static function init() {
		(new JDDJ_REST_Post_Controller('post'))->register_routes();
	}

}