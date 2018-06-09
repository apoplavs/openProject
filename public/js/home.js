
// якщо документ був повністю завантажений
$(document).ready(function () {
	//getJudgesList();
	//autocomplete(document.getElementById('search-input'));
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
	location.reload()
}


/**
 *
 * @returns {boolean}
 */
function updateJudgeStatus() {
	let judge = document.getElementById('judge-for-new-status').value;
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

function setJudgeToChangeStatus(judge) {
	document.getElementById('judge-for-new-status').value = judge;
}

