<?php

class JDDJ_REST_Post_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'posts';
	}

	/**
	 * Register the REST API routes.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_posts' ),
			)
		) );
	}

	/**
	 * Get a list of posts
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_posts( $request ) {

		$parameter_mappings = array (
			'category' => 'category_name',
			'page' => 'paged',
			'limit' => 'posts_per_page',
		);

		$parameters = array();

		foreach ($parameter_mappings as $param => $mapped_param) {
			if ($request->get_param($param) != null) {
				$parameters[$mapped_param] = $request->get_param($param);
			}
		}

		$posts = get_posts($parameters);

		$items = array_map(function (WP_Post $post) {
			$author = get_user_by('ID', $post->post_author);
			$item = array(
				'id' => $post->ID,
				'title' => get_the_title($post->ID),
				'exerpt' => get_the_excerpt($post->ID),
				'content' => wpautop($post->post_content),
				'status' => $post->post_status,
				'slug' => $post->post_name,
				'author' => (object) array(
					'id' => $author->ID,
					'name' => $author->display_name,
					'roles' => $author->roles
				),
				'createdAt' => $post->post_date,
				'updatedAt' => $post->post_modified
			);
			return (object) $item;
		}, $posts);

		return rest_ensure_response($items);

	}

}
