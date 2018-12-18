<?php

class PBJD_REST_Event_Controller extends WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'jddj/v1';
		$this->rest_base = 'events';
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
			'page' => 'paged',
			'limit' => 'posts_per_page',
			'order' => 'order',
			'orderby' => 'orderby'
		);

		$parameters = array('post_type' => 'event');

		foreach ($parameter_mappings as $param => $mapped_param) {
			if ($request->get_param($param) != null) {
				$parameters[$mapped_param] = $request->get_param($param);
			}
		}

		$posts = get_posts($parameters);

		if (!$request->get_param('page')) {
			$parameters_all = $parameters;
			$parameters_all['posts_per_page'] = -1;
			unset($parameters_all['paged']);
			$posts_all = get_posts($parameters_all);
			$posts_all_count = count($posts_all);
			header('X-WP-TotalPages: ' . ceil($posts_all_count / $parameters['posts_per_page']));
		}

		$items = array_map(function (WP_Post $post) {

			$content = do_shortcode(wptexturize(wpautop($post->post_content)));

			$posterUrl = get_the_post_thumbnail_url($post->ID) ?: null;

			if ($cdn_url = constant('CDN_URL')) {
				if ($posterUrl) {
					$posterUrl = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $posterUrl);
				}
				$content = preg_replace('/src="' . preg_quote(site_url(), '/') . '\/(.*?)\.(jpg|png|gif|mp3|mp4)"/', 'src="' . $cdn_url . '$1.$2"', $content);
			}

			if ($cdn_url_qpic = constant('CDN_URL_QPIC')) {
				$content = preg_replace('/https?:\/\/mmbiz.qpic.cn\//', $cdn_url_qpic, $content);
			}

			$item = array(
				'id' => $post->ID,
				'title' => get_the_title($post->ID),
				'content' => $content,
				'posterUrl' => $posterUrl,
				'open' => (bool) get_post_meta($post->ID, 'open', true),
				'createdAt' => $post->post_date,
				'updatedAt' => $post->post_modified
			);

			return (object) $item;

		}, $posts);

		return rest_ensure_response($items);

	}

}
