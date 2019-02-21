<?php

require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-appointments-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-attachments-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-cpc-review-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-events-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-misc-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-mottoes-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-organizations-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-posts-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-rooms-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-speeches-controller.php' );
require_once( PBA__PLUGIN_DIR . 'rest-controllers/class.pba-rest-spots-controller.php' );

class PBA_REST_API {

	public static function init() {
		(new PBA_REST_Appointment_Controller())->register_routes();
		(new PBA_REST_Attachment_Controller())->register_routes();
		(new PBA_REST_CPC_Review_Controller())->register_routes();
		(new PBA_REST_Event_Controller())->register_routes();
		(new PBA_REST_Misc_Controller())->register_routes();
		(new PBA_REST_Motto_Controller())->register_routes();
		(new PBA_REST_Organizations_Controller())->register_routes();
		(new PBA_REST_Post_Controller())->register_routes();
		(new PBA_REST_Room_Controller())->register_routes();
		(new PBA_REST_Speech_Controller())->register_routes();
		(new PBA_REST_Spot_Controller())->register_routes();
	}

}