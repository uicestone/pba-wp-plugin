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
			'supports' => array('title'),
			'menu_icon' => 'dashicons-megaphone',
			'has_archive' => true
		));

		register_post_type('motto', array(
			'label' => '座右铭',
			'labels' => array(
				'all_items' => '所有座右铭',
				'add_new' => '添加座右铭',
				'add_new_item' => '新座右铭',
				'not_found' => '未找到座右铭'
			),
			'public' => true,
			'supports' => array('title', 'editor'),
			'menu_icon' => 'dashicons-clipboard',
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

		add_filter('manage_motto_posts_columns', function($columns) {
			$columns['text'] = '文字';
			$columns['author_name'] = '作者';
			$columns['image'] = '图片';
			return $columns;
		});

		add_action('manage_motto_posts_custom_column', function($column_name) {
			global $post;
			switch ($column_name ) {
				case 'text' :
					echo $post->post_content;
					break;
				case 'author_name' :
					$author_name = get_post_meta($post->ID, 'author_name', true);
					echo $author_name;
					break;
				case 'image' :
					$image_url = get_post_meta($post->ID, 'image_url', true);
					echo '<a href="' . $image_url . '" target="_blank">' . basename($image_url) . '</a>';
					break;
				default;
			}
		});
	}

}
