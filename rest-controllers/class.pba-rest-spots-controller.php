<?php

class PBA_REST_Spot_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'pba/v1';
		$this->rest_base = 'spots';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/config', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_spot_config' ),
			)
		) );

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

	/**
	 * Map menus, types, etc.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_spot_config($request) {
		$spot_config = get_page_by_path('config', 'OBJECT', 'spot');

		$button_urls = array_map(function ($item) use ($spot_config) {
			return get_field('home_button_' . $item, $spot_config->ID);
		}, array('1', '2', '3', '4'));

		$spot_types = array_map(function ($item) use ($spot_config) {
			$text = get_field('spot_type_' . $item, $spot_config->ID);
			$result = array(
				'icon' => get_field('spot_type_icon_' . $item, $spot_config->ID),
				'text' => $text,
				'buttonIndex' => (int)$item
			);

			if ($item > 2) {
				$result['type'] = $text;
			}
			return $result;
		}, array('1', '2', '3', '4', '5'));

		$center_intro_text = get_post_meta($spot_config->ID, 'center_intro_text', true);

		return rest_ensure_response(array(
			'homeButtons' => $button_urls,
			'spotTypes' => $spot_types,
			'centerIntroText' => $center_intro_text
		));
	}

}
