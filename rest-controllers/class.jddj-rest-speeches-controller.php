<?php

class JDDJ_REST_Speech_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'speeches';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<type>.*)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_speeches' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_speech' ),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_speech' ),
			)
		) );
	}

	/**
	 * Get Speeches to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_speeches( $request ) {

	}

	/**
	 * Post a Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function post_speech( $request ) {

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$type = $request->get_param('type');
		$files =  $request->get_file_params();
		$body = $request->get_body_params();
		$bgid = $body['bgid'];

		if (!$bgid) {
			return rest_ensure_response(new WP_Error(400, 'Invalid bgid.', $bgid));
		}

		if (!in_array($type, array('talk', 'movie'))) {
			return rest_ensure_response(new WP_Error(400, 'Invalid speech type.', $type));
		}

		$file = wp_handle_upload($files['audio'], array('test_form' => false));

		if ( $file && empty( $file['error'] ) ) {
			$file['url'];
		} else {
			return rest_ensure_response(new WP_Error(400, $file['error']));
		}

		$speech_id = wp_insert_post(array(
			'post_type' => 'speech',
			'post_title' => $type === 'talk' ? '我要对党说' : '红色电影配音',
			'post_name' => $type  . '-' . time(),
			'post_status' => 'publish'
		));

		add_post_meta($speech_id, 'audio_url', $file['url']);
		add_post_meta($speech_id, 'bgid', $bgid);
		add_post_meta($speech_id, 'type', $type);

		return rest_ensure_response(array(
			'id' => $speech_id,
			'type' => $type,
			'bgid' => $bgid,
			'audioUrl' => $file['url'],
			'qrcodeUrl' => ''
		));
	}

	/**
	 * Delete a Speech to CPC
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_speech( $request ) {

	}

}
