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
}