/**
 * Script handles author selection on the post edit page.
 */
document.addEventListener("DOMContentLoaded", function () {

	const inputElement = document.getElementById('post_author_override_label');
	const hiddenField = document.getElementById('post_author_override');
	const ajaxUrl = author_metabox_params.ajax_url + '?action=author_search&nonce=' + author_metabox_params.nonce;

	let autocompleteTimer;

	inputElement.addEventListener('input', function () {
		const value = this.value;

		if (value.length >= 3) {
			clearTimeout(autocompleteTimer);

			autocompleteTimer = setTimeout(() => {
				fetchSuggestions(value);
			}, 500);
		}
	});

	function fetchSuggestions(query) {
		fetch(`${ajaxUrl}&term=${encodeURIComponent(query)}`)
			.then(response => response.json())
			.then(data => {
				showSuggestions(data);
			});
	}

	function showSuggestions(suggestions) {
		let dropdown = document.getElementById('autocomplete-dropdown');

		if (!dropdown) {
			dropdown = document.createElement('ul');
			dropdown.id = 'autocomplete-dropdown';
			dropdown.style.position = 'absolute';
			dropdown.style.zIndex = 1000;
			dropdown.style.border = '1px solid #ccc';
			dropdown.style.backgroundColor = '#fff';
			dropdown.style.padding = '0';
			dropdown.style.margin = '0';
			inputElement.parentNode.appendChild(dropdown);
		}

		dropdown.innerHTML = '';

		suggestions.forEach(suggestion => {
			const item = document.createElement('li');
			item.textContent = suggestion.label;
			item.style.padding = '5px';
			item.style.cursor = 'pointer';

			item.addEventListener('click', function () {
				inputElement.value = suggestion.label;
				hiddenField.value = suggestion.value;
				dropdown.remove();
			});

			dropdown.appendChild(item);
		});

		dropdown.style.width = inputElement.offsetWidth + 'px';
		dropdown.style.top = inputElement.offsetTop + inputElement.offsetHeight + 'px';
		dropdown.style.left = inputElement.offsetLeft + 'px';
	}

	document.addEventListener('click', function (event) {
		const dropdown = document.getElementById('autocomplete-dropdown');

		if (dropdown && !inputElement.contains(event.target)) {
			dropdown.remove();
		}
	});
});
