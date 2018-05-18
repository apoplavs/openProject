// підключення необхадного файлу з js
// залежно від того, на якій сторінці користувач
// noinspection SwitchStatementWithNoDefaultBranchJS
switch (window.location.pathname) {
	case '/judges':
		$.getScript("js/judges.js");
		break;
	/*case '/':
		break;
	case '/':
		break;
	case '/':
		break;*/
}