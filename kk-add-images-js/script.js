jQuery(document).ready(function($){
		//Добавляем аттрибут enctype к форме
	$('#commentform').attr('enctype', 'multipart/form-data');


		//Выводим сообщение о перелимите и очищаем форму если количество файлов больше допустимого
	$('#commentform').on('change', '#file', function (e) {
		var arr = ['image/jpeg', 'image/png', 'image/gif'];
		var max_images = kk_add_images.images;
		var files_list = $(this)[0].files;
		
		for (var i = 0; i < files_list.length; i++) {

			var image_type = arr.indexOf(files_list[i].type);

			if (image_type === -1) {
				alert('Вы можете загрузить только поддерживаемые типы файлов-изображений:\njpg, jpeg, png, gif');
			$(this).prop('value', null);
			}
		}
		if (files_list.length>max_images) {
			alert('Максимально возможное количество файлов для загрузки '+max_images);
			$(this).prop('value', null);
		}
	});


		//Инициализируем fancybox
	$("[data-fancybox]").fancybox({
		// Options will go here
	});

});