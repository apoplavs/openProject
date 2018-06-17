
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
			$('span.likes-unlikes').html(data);
		}
	});
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
			$('span.likes-unlikes').html(data);
		}
	});
}



// ФУНКЦІЇ ДЛЯ МАЛЮВАННЯ ГРАФІКІВ

/**
 * малювання графіка
 * загальної статистики по карегоріях розглянутих справ
 */
function showCommonStatistic () {
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawCommonStatistic);

	function drawCommonStatistic() {
		var data = google.visualization.arrayToDataTable([
			['категорія', 'кількість справ'],
			['цивільні',     statisticData.civil_amount],
			['кримінальні',      statisticData.criminal_amount],
			['справи про адміністративні правопорушення',  statisticData.adminoffence_amount],
			['адміністративні справи',  statisticData.admin_amount],
			['господарські справи',  statisticData.commercial_amount]

		]);
		var options = {
			title: 'Категорії розглянутих справ',
			is3D: true,
			legend:{position:'left'},
			chartArea:{left:0,top:0,width:'90%',height:'100%'}
		};
		var chart = new google.visualization.PieChart(document.getElementById('common-statistic'));
		chart.draw(data, options);
	}
}



function checkFile(form) {
	let fileInput = $('#newJudgePhoto')[0];
	let file = fileInput.files[0];
	let errHolder = $('.errorLabel');
	let fileExtension = file.name.substr(file.name.lastIndexOf('.') + 1).toLowerCase();

	if (file.size > 2050428) {
		errHolder.html('розмір файлу не повинен перевищувати 2mb');
		form.reset();
		return;
	}
	if (file.size < 40500) {
		errHolder.html('фото занадто низької якості');
		form.reset();
		return;
	}
	if (fileExtension != 'jpg' && fileExtension != 'jpeg' && fileExtension != 'png') {
		errHolder.html('допустимі типи файлів "jpg, jpeg, png"');
		form.reset();
		return;
	}
	errHolder.html('');
	// якщо помилок немає - показати фото для обрізки
	loadInView(file);
}

function loadInView(file) {
	// Создаем новый экземпляра FileReader
	let fileReader = new FileReader();

	// отримуємо обєкт img
	let preview = $('#uploaded-holder').find('>img');

	fileReader.onloadend = function () {
		preview[0].src = fileReader.result;
	};
	// Производим чтение картинки по URI
	fileReader.readAsDataURL(file);
	// document.getElementById('uploadPhotoForm').style.display = 'none';

	// обрізка зображеня
	// preview.imgAreaSelect({
	// 	aspectRatio: '1:1',
	// 	autoHide: true,
	// 	show: true,
	// 	minHeight: 100,
	// 	minWidth: 100,
	// 	x1: 10,
	// 	y1: 10,
	// 	x2: 200,
	// 	y2: 200,
	// 	// handles: true,
	// 	enable: true
	// 	// onSelectEnd: someFunction
	// });
}


function addPhoto(judge) {
	let formData = new FormData($('#uploadPhotoForm').find('form').get(0));
	// let fileInput = $('#newJudgePhoto')[0];
	// let file = fileInput.files[0];
	console.log(formData);
	// console.log($('#newJudgePhoto')[0]);

	$.ajax({
		url: '/judges/add-photo',
		type: 'post',
		contentType: false, // важно - убираем форматирование данных по умолчанию
		processData: false, // важно - убираем преобразование строк по умолчанию
		data: {	_token: $('meta[name="csrf-token"]').attr('content'),
		// photo: formData,
		judge: judge },
		success: function (data) {
			console.log(data);
			// errHolder.html('ok OK');
			// $('span.likes-unlikes').html(data);
		}
	});
}



// якщо документ був повністю завантажений
$(document).ready(function () {
	// якщо є статистика, малюємо графік по даному судді
	if (statisticData){
		showCommonStatistic();
	}
});
