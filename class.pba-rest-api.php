<?php

require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-posts-controller.php' );

class PBA_REST_API {

	public static function init() {
		(new PBA_REST_Post_Controller())->register_routes();
	}

}