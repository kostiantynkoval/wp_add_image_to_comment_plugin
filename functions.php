<?php


		//Подключаем скрипты и стили в пользовательской части
function kk_add_images_enqueue_scripts() {
	if (!is_single()) {
		return;
	}
	wp_enqueue_script('kk_add_images_script', plugins_url( '/kk-add-images-js/script.js', __FILE__ ), array('jquery'), null, true);
	wp_enqueue_style('kk_add_images_css', plugins_url( '/kk-add-images-css/style.css', __FILE__ ));
	wp_enqueue_script('kk_add_images_fancybox_script', plugins_url( '/dist/jquery.fancybox.min.js', __FILE__ ), array('jquery'), null, true);
	wp_enqueue_style('kk_add_images_fancybox_css', plugins_url( '/dist/jquery.fancybox.min.css', __FILE__ ));
	wp_localize_script( 'kk_add_images_script', 'kk_add_images', ['url' => admin_url( 'admin-ajax.php' ), 'images' => get_option( 'images_to_add' )] );
}

		//Добавляем аттрибут enctype к форме
function kk_add_images_edit_form_multipart_encoding($post) {

    echo ' enctype="multipart/form-data"';
    wp_die( 'dead' );
}


		//Добавляем форму загрузки файлов за полем комментариев
function kk_add_images_show_download_form( $field ){
	$field .= '<p class="comment-form-file"><label for="file">Выберите файл</label><input id="file" name="kk_add_images_files[]" type="file" multiple /></p>' . wp_nonce_field( 'kk_add_images_files[]', 'kk_add_image_nonce' );
	return $field;
}


		//Регистрируем поле настроек в админ-панели и создаем поле для указания максимального кол-ва изображений
function kk_add_images_register_option()
{
	add_options_page( 'Количество загружаемых изображений', 'Изображения в комментариях', 'manage_options', 'kk_add_image_quantity_option', 'kk_add_image_quantity_option_page' );
}


		//Код страницы настроек количества загружаемых изображений
function kk_add_image_quantity_option_page()
{
	if (isset($_POST['save'])) {
		update_option( 'images_to_add',  $_POST['images']  );
	}
	echo "<h2>Количество загружаемых изображений</h2>";
	echo "<form method=\"POST\"";
	echo "<p>Введите максимально возможное количество загружаемых изображений в комментарии<br>Минимальное значение: 1, максимальное: 20</p>";
	echo '<input type="number" min="1" max="20" name="images" value="'.get_option( 'images_to_add', 3 ).'" />';
	echo '<input type="submit" name="save" value="Сохранить" />';
}

		//Загружаем изображения в папку uploads и добавляем мета-поле в БД, а так же создаем копию изображения размером 300х300
function kk_add_images_upload_images($id)
{
	if ($_FILES['kk_add_images_files']['size'][0]==0) {return;}
	if( wp_verify_nonce( $_POST['kk_add_image_nonce'], 'kk_add_images_files[]' ) ){
		if ( ! function_exists( 'wp_handle_upload' ) ) 
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$overrides = array( 'test_form' => false );
	$file_list = $_FILES['kk_add_images_files'];

		
				//Реорганизуем массив загруженных файлов
		$file_ary = [];
    $file_count = count($file_list['name']);
    $file_keys = array_keys($file_list);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_list[$key][$i];
        }
    }

    for ($i = 0; $i < $file_count; $i++) {

            $file = $file_ary[$i];

            $move_file = wp_handle_upload($file, $overrides);

            if (!$move_file['error']) {
            
            			add_comment_meta( $id, 'kk_added_image', $file_ary[$i]['name'] );

			            $path = $move_file['file'];

			            $image_to_resize = wp_get_image_editor($move_file['url']);

			            if ( ! is_wp_error( $image_to_resize ) ) {
											// уменьшим её до размеров 80х80
											$image_to_resize->resize( 300, 300, true );
											// сохраним в корне сайта под названием new_image.png
											$image_to_resize->save(rename_resized_files($path));
									}
									
						}
						else
						{
							wp_die( "Ошибка загрузки файла!" );
						}

    }

}

		//Присваиваем новое имя для уменьшенного файла с суффиксом 300х300
function rename_resized_files($name)
{
	$ext = substr($name, stripos($name, '.'));
	$added = '-300x300' . $ext;
	$new_name = str_replace($ext, $added, $name);
	return $new_name;
}


		//Показываем изображения в тексте комментария
function kk_add_images_show_images_in_comment( $comment ){
	$time = get_comment_date('Y/m');
	$upload_dir = wp_upload_dir($time);
	if( $commentimages = get_comment_meta( get_comment_ID(), 'kk_added_image', false ) ) {
			$comment_add = '<div class="kk_add_images_container">';
			foreach ($commentimages as $image ) {
				$comment_add .= '<div class="kk_add_images_wrapper"><a href="'.$upload_dir["url"].'/'.$image.'" data-fancybox><img src="'.$upload_dir["url"].'/'.rename_resized_files($image).'"></a></div>';
			}
			$comment_add .= '</div>';
			$comment .= $comment_add;
  } 

	return $comment;
}


		//При удалении комментария из базы чистим папку uploads
function kk_add_images_clean_deleted( $id ){
	$time = get_comment_date('Y/m');
	$upload_dir = wp_upload_dir($time);
	if( $commentimages = get_comment_meta( $id, 'kk_added_image', false ) ) {
			
			foreach ($commentimages as $image ) {

				$small_file = $upload_dir["path"].'/'.rename_resized_files($image);
				$long_file = $upload_dir["path"].'/'.$image;


				if (file_exists($small_file)) {
					unlink($small_file);
				}
				if (file_exists($long_file)) {
					unlink($long_file);
				}

			}

	}
}


