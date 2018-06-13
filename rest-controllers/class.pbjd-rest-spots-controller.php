<?php

class PBJD_REST_Spot_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'spots';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_spots' ),
			)
		) );
	}

	/**
	 * Get all spots
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_spots( $request ) {

		$posts = get_posts(array('post_type' => 'spot', 'posts_per_page' => -1));

		$spots = array_map(function (WP_Post $post) {

			$spot = array(
				'id' => $post->ID,
				'type' => get_post_meta($post->ID, 'type', true),
				'name' => get_the_title($post->ID),
				'town' => get_post_meta($post->ID, 'town', true),
				'address' => get_post_meta($post->ID, 'address', true),
				'latitude' => (double) get_post_meta($post->ID, 'latitude', true),
				'longitude' => (double) get_post_meta($post->ID, 'longitude', true),
				'contact' => get_post_meta($post->ID, 'contact', true),
				'phone' => get_post_meta($post->ID, 'phone', true),
				'wechatPublicName' => get_post_meta($post->ID, 'wechat_public_name', true),
				'desc' => wpautop($post->post_content),
				'images' => array_map(function ($attachment) {
					return wp_get_attachment_url($attachment->ID);
				}, array_values(get_attached_media('image', $post))),
				'liveVideoUrl' => get_post_meta($post->ID, 'live_video_url', true)
			);

			return (object) $spot;

		}, $posts);

		return rest_ensure_response($spots);
	}

}
