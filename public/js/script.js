
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



// отримує відмічені фільтри і робить ajax запит
function getJudgesList(search) {
	getURL = '/judges-list';
	getURL += search || '';


	let regions = getRegions();
	let instances = getInstances();
	let reverseSort = document.getElementsByName('sorting')[0].checked ? 1 : 0;

	// console.log(getURL);
	// console.log(regions);
	// console.log(instances);
	// console.log(reverseSort);
	// ajax запит для отримання інфи
	$.get(getURL,
		{regions: regions, instances: instances, rsort: reverseSort},
		function (data, textStatus) {
			$('#judges-list').html(data);

			// при натисненні на кнопки пагінції робити
			// ajax запит на дану сторінку з врахуванням фільтрів
			$('ul.pagination>li>a').click(function (event) {
				event.preventDefault();
				getJudgesList(event.target.search)
			});

	}, "html");

	// console.log(reg);
	// console.log(instance);

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


// при натисненні на кнопки пагінції робити
// ajax запит на дану сторінку з врахуванням фільтрів
/*$('ul.pagination>li>a').click(function (event) {
	setPaginationListener(event);
});*/





$(document).ready(function () {
	getJudgesList();
});