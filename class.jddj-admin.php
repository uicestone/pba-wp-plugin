<?php

class JDDJ_Admin {

	public static function init() {
		self::register_post_types();
		self::manage_admin_columns();

	}

	protected static function register_post_types () {

		register_post_type('speech', array(
			'label' => '党建声音',
			'labels' => array(
				'all_items' => '所有党建声音',
				'add_new' => '添加党建声音',
				'add_new_item' => '新党建声音',
				'not_found' => '未找到党建声音'
			),
			'public' => true,
			'supports' => array('title', 'editor', 'revisions', 'thumbnail', 'page-attributes'),
			'menu_icon' => 'dashicons-megaphone',
			'has_archive' => true
		));
	}

	protected static function manage_admin_columns () {

		add_filter('manage_speech_posts_columns', function($columns) {
			$columns['bgid'] = '背景音乐';
			$columns['audio'] = '录音';
			return $columns;
		});

		add_action('manage_speech_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
				case 'bgid' :
					$bgid = get_post_meta($post->ID, 'bgid', true);
					echo $bgid;
					break;
				case 'audio' :
					$url = get_post_meta($post->ID, 'audio_url', true);
					echo '<audio src="' . $url . '" controls></audio>';
					break;
				default;
			}
		});
	}

}
