<?php

require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-cpc-review-controller.php' );
require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-mottoes-controller.php' );
require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-posts-controller.php' );
require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-speeches-controller.php' );
require_once( JDDJ__PLUGIN_DIR . 'rest-controllers/class.jddj-rest-spots-controller.php' );

class JDDJ_REST_API {

	public static function init() {
		(new JDDJ_REST_CPC_Review_Controller())->register_routes();
		(new JDDJ_REST_Motto_Controller())->register_routes();
		(new JDDJ_REST_Post_Controller())->register_routes();
		(new JDDJ_REST_Speech_Controller())->register_routes();
		(new JDDJ_REST_Spot_Controller())->register_routes();
	}

}