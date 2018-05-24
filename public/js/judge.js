
// якщо документ був повністю завантажений
$(document).ready(function () {
	// commonStatistic();
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
			url: '/bookmark/' + judge,
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
			url: '/bookmark/' + judge,
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



// ФУНКЦІЇ ДЛЯ МАЛЮВАННЯ ГРАФІКІВ

/**
 * малювання графіка
 * загальної статистики по карегоріях розглянутих справ
 */
function commonStatistic () {
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawCommonStatistic);

	function drawCommonStatistic() {
		var data = google.visualization.arrayToDataTable([
			['категорія', 'кількість справ'],
			['цивільні',     519],
			['кримінальні',      342],
			['справи про адміністративні правопорушення',  156]
		]);
		var options = {
			title: 'Категорії розглянутих справ',
			is3D: true,
			legend:{position:'left'},
			chartArea:{left:5,top:0,width:'80%',height:'100%'}
		};
		var chart = new google.visualization.PieChart(document.getElementById('common-statistic'));
		chart.draw(data, options);
	}
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawCommonStatistic);
}
