<?php

require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-attachments-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-cpc-review-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-misc-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-mottoes-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-posts-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-rooms-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-speeches-controller.php' );
require_once( PBJD__PLUGIN_DIR . 'rest-controllers/class.pbjd-rest-spots-controller.php' );

class PBJD_REST_API {

	public static function init() {
		(new PBJD_REST_Attachment_Controller())->register_routes();
		(new PBJD_REST_CPC_Review_Controller())->register_routes();
		(new PBJD_REST_Misc_Controller())->register_routes();
		(new PBJD_REST_Motto_Controller())->register_routes();
		(new PBJD_REST_Post_Controller())->register_routes();
		(new PBJD_REST_Room_Controller())->register_routes();
		(new PBJD_REST_Speech_Controller())->register_routes();
		(new PBJD_REST_Spot_Controller())->register_routes();
	}

}