<?php 

if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

		//При удалении комментария из базы чистим папку uploads

	global $wpdb;
	$rel_comments = $wpdb->get_results("SELECT * FROM $wpdb->commentmeta WHERE meta_key='kk_added_image'");

	if ($rel_comments) {
		
		foreach ($rel_comments as $rel_comment) {
		
			$time = get_comment_date('Y/m', $rel_comment->comment_id);
			$upload_dir = wp_upload_dir($time);


						$small_file = $upload_dir["url"].'/'.rename_resized_files($rel_comment->meta_value);
						$long_file = $upload_dir["url"].'/'.$rel_comment->meta_value;


						if (file_exists($small_file)) {
							
							echo "<pre>";
							var_dump($small_file);
							echo "</pre>";
							unlink($small_file);
						}
						if (file_exists($long_file)) {
							echo "<pre>";
							var_dump($long_file);
							echo "</pre>";
							unlink($long_file);
						}

		}

	}


 delete_option('kk_add_image_quantity_option');
	wp_dequeue_script( 'kk_add_images_script' );
