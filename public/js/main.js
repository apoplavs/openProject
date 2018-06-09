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

/**
 * функція яка виконується кожен раз
 * як тільки ajax завершить завантаження
 */
$(document).ajaxComplete(function() {
	setTimeout(function () {
		$('#floatingCirclesG').hide();

	}, 100);
});