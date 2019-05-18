
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

function getJurisdiction() {
	let jurisdictionEl = document.getElementsByName('jurisdiction');
	let jurisdictions = [];

	// перевіка відмічених юрисдикцій
	for (key in jurisdictionEl) {
		if (jurisdictionEl[key].checked) {
			jurisdictions.push(jurisdictionEl[key].value);
		}
	}
	return (jurisdictions);
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
	// запускаємо прелоадер
	ajaxActive = true;
	$('#loader').show();
	$('#content').addClass('blurry');

	getURL = '/judges-list';
	getURL += getParams || '';

	// отримуємо відмічені фільтри
	let instances = getInstances();
	let jurisdictions = getJurisdiction();
	let regions = getRegions();

	let search = getSearch();
	let reverseSort = document.getElementsByName('sorting')[0].checked ? 1 : 0;

	// ajax запит для отримання інфи
	$.get(getURL,
		{regions: regions, instances: instances, jurisdictions: jurisdictions, rsort: reverseSort, search: search},
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

// якщо прокрутка сторінки вниз фільтрів - закріпити кнопки
window.onscroll = function () {
	let applyFilters = $('#apply-filters');
	let isView = checkIfInView($('#apply-filters-mark'));
	if (isView) {
		applyFilters.css('position', 'static');
	} else {
		applyFilters.css('position', 'fixed');
	}
};

/**
 * перевірка чи знаходиться елемент в видимій області екрану
 * @param element
 * @returns {boolean}
 */
function checkIfInView(element){
	let offset = (element.offset().top + 60) - $(window).scrollTop();
	return offset <= window.innerHeight;
}




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

		/* getting values from DB via AJAX */
		$.ajax({
			url: '/judges-autocomplete',
			type: 'get',
			data: {search: val},
			dataType: "json",
			success: function (data, textStatus) {
				let a, b, i;

				/*close any already open lists of autocompleted values*/
				closeAllLists();
				if (!val) { return false;}
				currentFocus = -1;

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
						findJudge();
					});
					a.appendChild(b);
				}
			}
		});/* $.ajax (get) */
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

function setJudgeToChangeStatus(judge) {
	document.getElementById('judge-for-new-status').value = judge;
}

// завантаження списку суддів і активація поля автодоповнення
getJudgesList();
autocomplete(document.getElementById('search-input'));