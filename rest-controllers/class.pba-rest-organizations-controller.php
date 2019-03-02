<?php

class PBA_REST_Organizations_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'pba/v1';
		$this->rest_base = 'organizations';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_organizations' ),
			)
		) );
	}

	/**
	 * Get a list of posts
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_organizations( $request ) {

		$parameters = array('post_type' => 'organization', 'posts_per_page' => -1);

		$posts = get_posts($parameters);

		$departments = array_map(function (WP_Post $post) {

			$posterUrl = get_the_post_thumbnail_url($post->ID) ?: null;

			if (defined('CDN_URL')) {
				$cdn_url = constant('CDN_URL');
				if ($posterUrl) {
					$posterUrl = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $posterUrl);
				}
			}

			$departments = get_field('departments', $post->ID);

			$departments = array_map(function($department) {

				$posterUrl = get_the_post_thumbnail_url($department->ID) ?: null;

				if (defined('CDN_URL')) {
					$cdn_url = constant('CDN_URL');
					if ($posterUrl) {
						$posterUrl = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $posterUrl);
					}
				}

				return array(
					'id' => $department->ID,
					'title' => $department->post_title,
					'posterUrl' => $posterUrl
				);
			}, $departments);

			$organization = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'posterUrl' => $posterUrl,
				'departments' => $departments
			);

			return (object) $organization;

		}, $posts);

		return rest_ensure_response($departments);

	}

}
