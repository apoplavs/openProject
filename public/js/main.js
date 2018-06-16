// індикатор мережевої активності ajax
let ajaxActive = false;
/**
 * функція яка виконується кожен раз
 * як тільки ajax починає будь-яке завантаження
 */
$(document).ajaxStart(function() {
	ajaxActive = true;
	$('#loader').show();
	$('#content').addClass('blurry');
});


/**
 * функція яка виконується кожен раз
 * як тільки ajax завершить всі завантаження
 */
$(document).ajaxStop(function() {
	ajaxActive = false;
	$('#loader').hide(200);
	$('#content').removeClass('blurry');
});

/**
 * функція яка виконується кожен раз
 * як тільки документ буде завантажено
 * і ajax не буде мати активних завантажень
 */
$(document).ready(function() {
	if (!ajaxActive) {
		$('#loader').hide();
		$('#content').removeClass('blurry');
	}
});











// підключення необхадного файлу з js
// залежно від того, на якій сторінці користувач
// noinspection SwitchStatementWithNoDefaultBranchJS
switch (window.location.pathname) {
	case '/judges':
		$.getScript("js/judges.js");
		break;
	case '/home':
		$.getScript("js/home.js");
		break;
	/*case '/':
		break;
	case '/':
		break;
	case '/':
		break;*/
}




