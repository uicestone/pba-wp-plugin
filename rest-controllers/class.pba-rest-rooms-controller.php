<?php

class PBA_REST_Room_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'pba/v1';
		$this->rest_base = 'rooms';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_rooms' ),
			)
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<number>.+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_room' ),
			)
		) );

	}

	/**
	 * Get all rooms
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_rooms( $request ) {

		$parameters = array('post_type' => 'room', 'posts_per_page' => -1, 'orderby' => 'ID', 'order' => 'asc');

		if ($request->get_param('floor')) {
			$parameters['meta_key'] = 'floor';
			$parameters['meta_value'] = $request->get_param('floor');
		}

		$posts = get_posts($parameters);

		$rooms = array_map(function (WP_Post $post) {

			$room = array(
				'id' => $post->ID,
				'floor' => (int) get_post_meta($post->ID, 'floor', true),
				'number' => get_post_meta($post->ID, 'number', true),
				'title' => get_the_title($post->ID),
				'open' => (boolean) get_post_meta($post->ID, 'open', true),
				'color' => get_post_meta($post->ID, 'color', true),
				'hint' => get_post_meta($post->ID, 'hint', true),
				'content' => wpautop($post->post_content),
				'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full') ?: null
			);

			return (object) $room;

		}, $posts);

		return rest_ensure_response($rooms);
	}

	/**
	 * Get single Room
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_room( $request ) {

		$number = $request->get_param('number');

		$post = get_posts(array('post_type' => 'room', 'meta_key' => 'number', 'meta_value' => $number))[0];

		if (!$post) {
			return rest_ensure_response(new WP_Error(404, 'Not found.', $number));
		}

		$room = array(
			'id' => $post->ID,
			'floor' => (int) get_post_meta($post->ID, 'floor', true),
			'number' => get_post_meta($post->ID, 'number', true),
			'title' => get_the_title($post->ID),
			'open' => (boolean) get_post_meta($post->ID, 'open', true),
			'color' => get_post_meta($post->ID, 'color', true),
			'hint' => get_post_meta($post->ID, 'hint', true),
			'content' => wpautop($post->post_content),
			'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full') ?: null,
			'appointments' => json_decode(get_post_meta($post->ID, 'appointments', true)),
			'fullDates' => json_decode(get_post_meta($post->ID, 'full_dates', true)),
		);

		return rest_ensure_response((object) $room);
	}

}
