jQuery(document).ready(function($){
		//Добавляем аттрибут enctype к форме
	$('#commentform').attr('enctype', 'multipart/form-data');


		//Выводим сообщение о перелимите и очищаем форму если количество файлов больше допустимого
	$('#commentform').on('change', '#file', function (e) {
		var max_images = kk_add_images.images;
		console.log($(this)[0].files[0].name);
		if ($(this)[0].files.length>max_images) {
			alert('Максимально возможное количество файлов для загрузки '+max_images);
			$(this).prop('value', null);
		}
	});

});