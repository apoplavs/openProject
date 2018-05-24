
function getInstances() {
	let instanceEl = document.getElementsByName('instance');
	let instances = [];

	// перевіка відмічених інстанцій
	for (key in instanceEl) {
		if (instanceEl[key].checked) {
			instances.push(instanceEl[key].value);
		}
	}
	return (instances);
}

function getRegions() {
	let regionsEl = document.getElementsByName('region');
	let regions = [];

	// перевіка відмічених регіонів
	for (key in regionsEl) {
		if (regionsEl[key].checked) {
			regions.push(regionsEl[key].value);
		}
	}
	return (regions);
}

function getSearch() {
	let searchSentence = document.getElementById('search-input').value;
	let search = '';

	if (searchSentence.length > 1) {
		search = searchSentence.split(' ')[0];
	}
	return (search);

}
// отримує відмічені фільтри і робить ajax запит
function getJudgesList(getParams) {
	getURL = '/judges-list';
	getURL += getParams || '';

	let regions = getRegions();
	let instances = getInstances();
	let search = getSearch();
	let reverseSort = document.getElementsByName('sorting')[0].checked ? 1 : 0;

	// ajax запит для отримання інфи
	$.get(getURL,
		{regions: regions, instances: instances, rsort: reverseSort, search: search},
		function (data, textStatus) {
			$('#judges-list').html(data);

			// при натисненні на кнопки пагінції робити
			// ajax запит на дану сторінку з врахуванням фільтрів
			$('ul.pagination>li>a').click(function (event) {
				event.preventDefault();
				getJudgesList(event.target.search)
			});

	}, "html");
}

// change sign and call applyFilters()
function changeSorting(checked) {
	if (checked) {
		$('i.fa-sort-alpha-asc').addClass('fa-sort-alpha-desc');
		$('i.fa-sort-alpha-desc').removeClass('fa-sort-alpha-asc');
	} else {
		$('i.fa-sort-alpha-desc').addClass('fa-sort-alpha-asc');
		$('i.fa-sort-alpha-asc').removeClass('fa-sort-alpha-desc');
	}
	getJudgesList();
}
// якщо документ був повністю завантажений
$(document).ready(function () {
	getJudgesList();
	autocomplete(document.getElementById('search-input'));
});


/**
 * весь функціонал автодоповнення в полі пошуку
 * @param inp обєкт поля для пошуку
 */
function autocomplete(inp) {
	/*the autocomplete function takes two arguments,
	the text field element and an array of possible autocompleted values:*/
	var currentFocus;
	/*execute a function when someone writes in the text field:*/
	inp.addEventListener("input", function(e) {
		var val = this.value;

		/*close any already open lists of autocompleted values*/
		closeAllLists();
		if (!val) { return false;}
		currentFocus = -1;

		/* getting values from DB via AJAX */
		$.get('/judges-autocomplete',
			{search: val},
			function (data, textStatus) {
				let a, b, i;
				/*create a DIV element that will contain the items (values):*/
				a = document.createElement("DIV");
				a.setAttribute("id", inp.id + "autocomplete-list");
				a.setAttribute("class", "autocomplete-items");
				/*append the DIV element as a child of the autocomplete container:*/
				inp.parentNode.appendChild(a);
				/*for each item in the array...*/
				for (i = 0; i < data.length; i++) {
					/*create a DIV element for each matching element:*/
					b = document.createElement("DIV");
					judgeName = data[i].surname.substr(val.length)
						+ ' ' + (data[i].name.length > 2 ? data[i].name + ' ' : data[i].name + '. ')
						+ (data[i].patronymic.length > 2 ? data[i].patronymic + ' ' : data[i].patronymic + '. ');
					/*make the matching letters bold:*/
					b.innerHTML = "<strong>" + data[i].surname.substr(0, val.length) + "</strong>";
					b.innerHTML += judgeName;
					b.innerHTML += "<small class='text-muted ml-1'>(" + data[i].court_name + ")</small>";
					/*insert a input field that will hold the current array item's value:*/
					b.innerHTML += "<input type='hidden' value='" + data[i].surname.substr(0, val.length) + judgeName + "'>";
					/*execute a function when someone clicks on the item value (DIV element):*/
					b.addEventListener("click", function(e) {
						/*insert the value for the autocomplete text field:*/
						inp.value = this.getElementsByTagName("input")[0].value;
						/*close the list of autocompleted values,
						(or any other open lists of autocompleted values:*/
						closeAllLists();
					});
					a.appendChild(b);
				}
			}, "json"
		);/* $.get */
	});

	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener("keydown", function(e) {
		var x = document.getElementById(this.id + "autocomplete-list");
		if (x) x = x.getElementsByTagName("div");
		if (e.keyCode == 40) {
			/*If the arrow DOWN key is pressed,
			increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 38) { //up
			/*If the arrow UP key is pressed,
			decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
			}
		}
	});

	function addActive(x) {
		/*a function to classify an item as "active":*/
		if (!x) return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length) currentFocus = 0;
		if (currentFocus < 0) currentFocus = (x.length - 1);
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add("autocomplete-active");
	}
	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}
	function closeAllLists(elmnt) {
		/*close all autocomplete lists in the document,
		except the one passed as an argument:*/
		var x = document.getElementsByClassName("autocomplete-items");
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}
	/*execute a function when someone clicks in the document:*/
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}

/**
 * виконується при натисненні на кнопку "знайти"
 * в полі пошуку судді
 */
function findJudge() {
	let reverseSort = document.getElementsByName('sorting')[0].checked;
	$('#form-filters')[0].reset();
	document.getElementsByName('sorting')[0].checked = reverseSort;
	getJudgesList();
}


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

