<?php

class JDDJ_REST_Speech_Controller extends WP_REST_Posts_Controller {
	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( 'jddj/v1', '/speeches', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_speeches' ),
			), array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'post_speech' ),
				'args' => array(
					'key' => array(
						'required' => true,
						'type' => 'string',
						'sanitize_callback' => array( $this, 'sanitize_key' ),
						'description' => '',
					),
				),
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
