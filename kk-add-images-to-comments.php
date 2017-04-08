<?php
/*
Plugin Name: Add Image to Comment
Plugin URI: https://github.com/kostiantynkoval/wp_add_image_to_comment_plugin
Description: Gets opportunity to add images to user's comments
Version: 1.0
Author: Kostiantyn Koval
*/

require_once __DIR__ . '/functions.php';

			//Подключаем скрипты и стили в пользовательской части
add_action('wp_enqueue_scripts', 'kk_add_images_enqueue_scripts');


		//Добавляем форму загрузки файлов за полем комментариев
add_filter( 'comment_form_field_comment', 'kk_add_images_show_download_form' );


		//Регистрируем поле настроек в админ-панели и создаем поле для указания максимального кол-ва изображений
add_action('admin_menu', 'kk_add_images_register_option');


		//Загружаем изображения в БД
add_action( 'comment_post', 'kk_add_images_upload_images');


		//Показываем изображения в тексте комментария
add_filter( 'comment_text', 'kk_add_images_show_images_in_comment', 10, 3 );


		//При удалении комментария из базы чистим папку
add_action( 'delete_comment', 'kk_add_images_clean_deleted' );



