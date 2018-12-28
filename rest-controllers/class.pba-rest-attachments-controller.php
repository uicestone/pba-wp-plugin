<?php

class PBA_REST_Attachment_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'pba/v1';
		$this->rest_base = 'attachments';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_attachments' ),
			)
		) );
	}

	/**
	 * Get a list of posts
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_attachments( $request ) {

		$parameter_mappings = array (
			'category' => 'category_name',
			'page' => 'paged',
			'limit' => 'posts_per_page',
			'order' => 'order',
			'orderby' => 'orderby'
		);

		$parameters = array();

		foreach ($parameter_mappings as $param => $mapped_param) {
			if ($request->get_param($param) != null) {
				$parameters[$mapped_param] = $request->get_param($param);
			}
		}

		$parameters['post_type'] = 'attachment';

		$posts = get_posts($parameters);

		$items = array_map(function (WP_Post $post) {
			$mime = get_post_mime_type($post->ID);
			preg_match('/^(.*)\//', $mime, $matches);
			$type = $matches[1];
			$url = wp_get_attachment_url($post->ID);

			if ($cdn_url = constant('CDN_URL')) {
				$url = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $url);
			}

			$item = array(
				'id' => $post->ID,
				'title' => get_the_title($post->ID),
				'type' => $type,
				'mime' => $mime,
				'categories' => array_map(function ($category) {
					return $category->name;
				}, get_the_category($post->ID)),
				'url' => $url,
				'createdAt' => $post->post_date,
				'updatedAt' => $post->post_modified
			);
			return (object) $item;
		}, $posts);

		return rest_ensure_response($items);

	}

}
