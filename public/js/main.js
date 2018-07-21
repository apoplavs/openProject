// індикатор мережевої активності ajax
let ajaxActive = false;
/**
 * функція яка виконується кожен раз
 * як тільки ajax починає будь-яке завантаження
 */
$(document).ajaxStart(function() {
	//
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




/**
 * додати або видалити суддю з закладок
 * @param el
 * @param judge
 */
function addBookmark(el, judge) {
	let iTag = el.getElementsByTagName('i')[0];
	let spanTag = el.getElementsByTagName('span')[0];

	if (iTag.classList.contains('fa-bookmark')) {
		iTag.classList.add('fa-bookmark-o');
		iTag.classList.remove('fa-bookmark');
		spanTag.innerHTML = 'відстежувати';
		$.ajax({
			url: '/judges/' + judge + '/bookmark',
			type: 'post',
			data: {_method: 'DELETE',
				_token : $('meta[name="csrf-token"]').attr('content'), },
			success: function (data) {
			}
		});
	} else if (iTag.classList.contains('fa-bookmark-o')) {
		iTag.classList.add('fa-bookmark');
		iTag.classList.remove('fa-bookmark-o');
		spanTag.innerHTML = 'відстежується';
		$.ajax({
			url: '/judges/' + judge + '/bookmark',
			type: 'post',
			data: {_method: 'PUT',
				_token : $('meta[name="csrf-token"]').attr('content'), },
			success: function (data) {
			}
		});
	}
}



/**
 * встановлює новий статус для судді
 * @returns {boolean}
 */
function updateJudgeStatus(judge) {
	if (judge == 0) {
		return false;
	}
	let newStatus = document.getElementById('chooser-judge-status').value;
	let dueDate = document.getElementById('status-end-date').value;

	$.ajax({
		url: '/judge-status/' + judge,
		type: 'post',
		data: {_method: 'PUT',
			_token : $('meta[name="csrf-token"]').attr('content'),
			setstatus: newStatus,
			date: dueDate},
		success: function (data) {
			$('#judge'+judge).html(data);
		}
	});
}



/**
 * поставити/прибрати лайк судді
 * @returns {boolean}
 */
function putLike(judge) {

	$.ajax({
		url: '/judges/' + judge + '/like',
		type: 'post',
		data: {_method: 'PUT',
			_token : $('meta[name="csrf-token"]').attr('content')},
		success: function (data) {
		}
	});
	// костиль чисто для презентації, в робочій версії потрібно зробити без перезавантаження сторінки
	location.reload();
}

/**
 * поставити/прибрати дизлайк судді
 * @returns {boolean}
 */
function putUnlike(judge) {

	$.ajax({
		url: '/judges/' + judge + '/unlike',
		type: 'post',
		data: {_method: 'PUT',
			_token: $('meta[name="csrf-token"]').attr('content')},
		success: function (data) {
		}
	});
	// костиль чисто для презентації, в робочій версії потрібно зробити без перезавантаження сторінки
	location.reload();
}












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




