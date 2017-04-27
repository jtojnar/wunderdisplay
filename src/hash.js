{contentType javascript}
var sheet = document.getElementById('dynamic');

function updateTagVisibility() {
	if (location.hash === '' || location.hash === '#') {
		sheet.innerText = '';
	} else {
		var hash = location.hash.toString().replace(/^#/, '');
		sheet.innerText = 'li:not(.' + hash + ') { display: none; }';
	}
}

window.addEventListener('hashchange', updateTagVisibility, false);
updateTagVisibility();
