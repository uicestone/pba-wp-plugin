<?php

class PBJD_REST_Appointment_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'appointments';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_appointments' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_appointment' ),
			), array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_appointment' ),
			)
		) );

	}

	/**
	 * Get a list of appointments
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_appointments( $request ) {

		$parameters = array('post_type' => 'appointment', 'limit' => -1);

		$posts = get_posts($parameters);

		$appointments = array_map(function (WP_Post $post) {
			$appointment = array(
				'id' => $post->ID,
				'type' => get_post_meta($post->ID, 'type', true)
			);
			return (object) $appointment;
		}, $posts);

		return rest_ensure_response($appointments);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function post_appointment( $request ) {

		$body = $request->get_body_params();
		return rest_ensure_response($body);

		$appointment_id = wp_insert_post(array(
			'post_type' => 'appointment',
			'post_status' => 'publish'
		));

		foreach ($body as $key => $value) {
			add_post_meta($appointment_id, $key, $value);
		}

		return rest_ensure_response(array(
			'id' => $appointment_id,
			'type' => get_post_meta($appointment_id, 'type', true)
		));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function delete_appointment( $request ) {

	}

}
