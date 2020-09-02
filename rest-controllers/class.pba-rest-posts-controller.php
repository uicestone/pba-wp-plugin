<?php

class PBA_REST_Post_Controller extends WP_REST_Controller {

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

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>.+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_post' ),
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
			'order' => 'order',
			'orderby' => 'orderby'
		);

		$parameters = array();

		foreach ($parameter_mappings as $param => $mapped_param) {
			if ($request->get_param($param) != null) {
				$parameters[$mapped_param] = $request->get_param($param);
			}
		}

		if ($month = $request->get_param('month')) {
			$parameters['meta_query'] = array(
				array('key' => 'date', 'compare' => 'LIKE', 'value' => $month)
			);
		}

		if ($gongyi = $request->get_param('gongyi')) {

			$posts = get_field('events', $gongyi);

		} else {
			$posts = get_posts($parameters);

			$parameters_all = $parameters;
			$parameters_all['posts_per_page'] = -1;
			unset($parameters_all['paged']);
			$posts_all = get_posts($parameters_all);
			$posts_all_count = count($posts_all);
			header('X-WP-TotalPages: ' . ceil($posts_all_count / $parameters['posts_per_page']));
		}


		$items = array_map(function (WP_Post $post) use($request) {
			$author = get_user_by('ID', $post->post_author);
			$town_category = get_category_by_slug('town');
			$town = null;
			$categories = array_map(function ($category) use (&$town, $town_category) {
				if ($category->parent === $town_category->cat_ID) {
					$town = $category->name;
				}
				return $category->name;
			}, get_the_category($post->ID));

			if ($request->get_param('standaloneContentImages')) {
				preg_match_all('/<img.*>/', $post->post_content, $matches);
				// exclude 'qr' image
				$images = array_map(function($img){
					preg_match('/src="(.*?)"/', $img, $matches);
					return $matches[1];
				},array_values(array_filter($matches[0], function($match){
					return !preg_match('/class=".*?qr.*?"/', $match);
				})));
				$post->post_content = preg_replace('/<img.*>\n?/', '', $post->post_content);
			}

			$content = do_shortcode(wptexturize(wpautop($post->post_content)));
			$posterUrl = get_the_post_thumbnail_url($post->ID) ?: null;

			if (defined('CDN_URL')) {
				$cdn_url = constant('CDN_URL');
				if ($posterUrl) {
					$posterUrl = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $posterUrl);
				}
				$content = preg_replace('/src="' . preg_quote(site_url(), '/') . '\/(.*?)\.(jpg|png|gif|mp3|mp4)"/', 'src="' . $cdn_url . '$1.$2"', $content);
			}

			if (defined('CDN_URL_QPIC')) {
				$cdn_url_qpic = constant('CDN_URL_QPIC');
				$content = preg_replace('/https?:\/\/mmbiz.qpic.cn\//', $cdn_url_qpic, $content);
			}

			$item = array(
				'id' => $post->ID,
				'title' => get_the_title($post->ID),
				'excerpt' => get_the_excerpt($post->ID),
				'content' => $content,
				'status' => $post->post_status,
				'slug' => $post->post_name,
				'posterUrl' => $posterUrl,
				'categories' => $categories,
				'town' => $town,
				'author' => (object) array(
					'id' => $author->ID,
					'name' => $author->display_name,
					'roles' => $author->roles
				),
				'createdAt' => $post->post_date,
				'updatedAt' => $post->post_modified
			);

			if (isset($images)) {
				$item['images'] = $images;
			}

			if ($date = get_post_meta($post->ID, 'date', true)) {
				$item['date'] = $date;
			}

			return (object) $item;

		}, $posts);

		return rest_ensure_response($items);

	}

	/**
	 * Get single post detail
	 * @param WP_REST_Request $request
	 * @return WP_Error|WP_REST_Response
	 */
	public static function get_post( $request ) {

		$id = $request->get_param('id');

		if (is_numeric($id)) {
			$post = get_post($id);
		} else {
			$post = get_page_by_path($id);
		}

		$author = get_user_by('ID', $post->post_author);
		$town_category = get_category_by_slug('town');
		$town = null;
		$categories = array_map(function ($category) use (&$town, $town_category) {
			if ($category->parent === $town_category->cat_ID) {
				$town = $category->name;
			}
			return $category->name;
		}, get_the_category($post->ID));

		$content = do_shortcode(wptexturize(wpautop($post->post_content)));
		$posterUrl = get_the_post_thumbnail_url($post->ID) ?: null;

		if (defined('CDN_URL')) {
			$cdn_url = constant('CDN_URL');
			if ($posterUrl) {
				$posterUrl = preg_replace('/' . preg_quote(site_url(), '/') . '\//', $cdn_url, $posterUrl);
			}
			$content = preg_replace('/src="' . preg_quote(site_url(), '/') . '\/(.*?)\.(jpg|png|gif|mp3|mp4)"/', 'src="' . $cdn_url . '$1.$2"', $content);
		}

		if (defined('CDN_URL_QPIC')) {
			$cdn_url_qpic = constant('CDN_URL_QPIC');
			$content = preg_replace('/https?:\/\/mmbiz.qpic.cn\//', $cdn_url_qpic, $content);
		}

		$item = array(
			'id' => $post->ID,
			'title' => get_the_title($post->ID),
			'excerpt' => get_the_excerpt($post->ID),
			'content' => $content,
			'status' => $post->post_status,
			'slug' => $post->post_name,
			'posterUrl' => $posterUrl,
			'categories' => $categories,
			'town' => $town,
			'author' => (object) array(
				'id' => $author->ID,
				'name' => $author->display_name,
				'roles' => $author->roles
			),
			'createdAt' => $post->post_date,
			'updatedAt' => $post->post_modified
		);

		if ($date = get_post_meta($post->ID, 'date', true)) {
			$item['date'] = $date;
		}

		return rest_ensure_response($item);

	}

}
