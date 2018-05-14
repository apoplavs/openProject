

// filters
function applyFilters() {
	let instance = [];
	let reg = [];
	let regions = document.getElementsByName('region');
	let instances = document.getElementsByName('instance');

	// перевіка відмічених регіонів
	for (key in regions) {
		if (regions[key].checked) {
			reg.push(regions[key].value);
		}
	}

	// перевіка відмічених інстанцій
	for (key in instances) {
		if (instances[key].checked) {
			instance.push(instances[key].value);
		}
	}

	console.log(reg);
	console.log(instance);

}



$(document).ready(function () {
	//
});