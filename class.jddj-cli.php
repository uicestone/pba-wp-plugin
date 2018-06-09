<?php

WP_CLI::add_command( 'jddj', 'JDDJ_CLI' );

/**
 * Filter spam comments.
 */
class JDDJ_CLI extends WP_CLI_Command {

	/**
	 * Download all cpc history reviews.
	 *
	 * ## EXAMPLES
	 *
	 *     wp jddj download-review
	 *
	 */
	public function download_review() {
		$cpc_review_urls = null;
		require JDDJ__PLUGIN_DIR . 'resource/cpc-review.php';
		foreach ($cpc_review_urls as $month => $urls) {
			foreach ($urls as $day => $url) {
				WP_CLI::line( 'Attempting: ' . $month . ' ' . $day );
				$page_raw = file_get_contents($url);
				$page = iconv('gb2312', 'utf-8//IGNORE', $page_raw);
				preg_match('/\<h1 class\="red"\>党史上的今天\<\/h1\>\s*([\s\S]*?)<\/div>/', $page, $matches);
				if (!$matches) {
					WP_CLI::line( var_export($page_raw, true) ); exit;
				}
				$post_id = wp_insert_post(array(
					'post_status' => 'publish',
					'post_title' => '党史上的那一天：' . $month . '月' . $day . '日',
					'post_name' => 'cpc-review-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT),
					'post_content' => $matches[1],
					'post_category' => ['cpc-review']
				));
				WP_CLI::line( 'Post inserted: ' . $post_id );

			}
		}

	}

	/**
	 * Convert _spot table to posts.
	 *
	 * ## EXAMPLES
	 *
	 *     wp jddj download-review
	 *
	 */
	public function convert_spots() {
		global $wpdb;
		$spots = $wpdb->get_results("select * from `_spots` where `名称` != ''");
		foreach ($spots as $spot) {

			$name = trim($spot->名称);
			$type = trim($spot->类型);
			$town = trim($spot->所属街镇);
			$intro = trim($spot->介绍);
			$address = trim($spot->地址);
			$contact = trim($spot->联系人);
			$phone = trim($spot->电话);
			$wechat_public_name = trim($spot->公众号名称);

			$post = get_posts(array('post_type' => 'spot', 'name' => $name, 'meta_key' => 'type', 'meta_value' => $type))[0];

			if ($post) {
				continue;
			}

			$post_id = wp_insert_post(array(
				'post_type' => 'spot',
				'post_name' => $name,
				'post_title' => $name,
				'post_content' => $intro,
				'post_status' => 'publish'
			));

			$metas = compact('town', 'type', 'address', 'contact', 'phone', 'wechat_public_name');

			foreach ($metas as $key => $value) {
				if (!$value) {
					continue;
				}
				add_post_meta($post_id, $key, $value);
			}

			WP_CLI::line( 'Inserted ' . $type . ': ' . $name . ' to post ' . $post_id . '.');

		}
	}
}