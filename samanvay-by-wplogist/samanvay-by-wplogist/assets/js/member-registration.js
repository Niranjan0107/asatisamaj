(function () {
	'use strict';
	var form = document.getElementById('samanvay-registration-form');
	var state = document.getElementById('member-state');
	var district = document.getElementById('member-district');
	var tehsil = document.getElementById('member-tehsil');
	var locations = [];
	function option(value, label) { var item = document.createElement('option'); item.value = value; item.textContent = label; return item; }
	function reset(select, label) { select.innerHTML = ''; select.appendChild(option('', label)); select.disabled = true; }
	if (state && window.samanvayMembership) {
		fetch(samanvayMembership.locationsUrl, { credentials: 'same-origin' }).then(function (response) { if (!response.ok) { throw new Error('locations'); } return response.json(); }).then(function (data) {
			locations = Array.isArray(data) ? data : [];
			locations.forEach(function (item) { state.appendChild(option(item.state, item.state)); });
			state.disabled = false;
			if (samanvayMembership.selected && samanvayMembership.selected.state) {
				state.value = samanvayMembership.selected.state;
				state.dispatchEvent(new Event('change'));
				district.value = samanvayMembership.selected.district || '';
				district.dispatchEvent(new Event('change'));
				tehsil.value = samanvayMembership.selected.tehsil || '';
			}
		}).catch(function () { reset(state, samanvayMembership.locationError); });
		state.addEventListener('change', function () {
			reset(district, 'जिला चुनें'); reset(tehsil, 'पहले जिला चुनें');
			var selected = locations.find(function (item) { return item.state === state.value; });
			if (!selected) { return; }
			selected.districts.forEach(function (item) { district.appendChild(option(item.name, item.name)); }); district.disabled = false;
		});
		district.addEventListener('change', function () {
			reset(tehsil, 'ब्लॉक / तहसील चुनें');
			var selectedState = locations.find(function (item) { return item.state === state.value; });
			var selectedDistrict = selectedState && selectedState.districts.find(function (item) { return item.name === district.value; });
			if (!selectedDistrict) { return; }
			(selectedDistrict.sub_districts || []).forEach(function (item) { tehsil.appendChild(option(item, item)); }); tehsil.disabled = false;
		});
	}
	document.querySelectorAll('.membership-password-toggle').forEach(function (button) { button.addEventListener('click', function () { var input = button.parentNode.querySelector('input'); var visible = input.type === 'password'; input.type = visible ? 'text' : 'password'; button.classList.toggle('is-visible', visible); button.setAttribute('aria-pressed', visible ? 'true' : 'false'); button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password'); }); });
	if (form) { form.addEventListener('submit', function (event) { var error = form.querySelector('.membership-form-error'); error.hidden = true; if (!form.checkValidity()) { event.preventDefault(); form.reportValidity(); } }); }
	var employmentSector = document.getElementById('member-employment-sector');
	var gender = document.getElementById('member-gender') || document.getElementById('dashboard-gender');
	function updateEmploymentOptions() {
		if (!employmentSector || !gender) { return; }
		var housewife = employmentSector.querySelector('option[value="housewife"]');
		var showHousewife = gender.value === 'female';
		if (!housewife) { return; }
		housewife.hidden = !showHousewife;
		housewife.disabled = !showHousewife;
		if (!showHousewife && employmentSector.value === 'housewife') { employmentSector.value = ''; }
	}
	if (gender && employmentSector) { gender.addEventListener('change', updateEmploymentOptions); updateEmploymentOptions(); }
	var upload = document.getElementById('payment-screenshot');
	if (upload) { upload.addEventListener('change', function () { var output = document.querySelector('.membership-file-name'); output.textContent = upload.files.length ? upload.files[0].name : ''; }); }
	document.querySelectorAll('.membership-upload').forEach(function (paymentForm) {
		var transaction = paymentForm.querySelector('[name="transaction_id"]');
		var screenshot = paymentForm.querySelector('[name="payment_screenshot"]');
		if (!transaction || !screenshot) { return; }
		function clearProofError() { transaction.setCustomValidity(''); }
		transaction.addEventListener('input', clearProofError);
		screenshot.addEventListener('change', clearProofError);
		paymentForm.addEventListener('submit', function (event) {
			clearProofError();
			if (!transaction.value.trim() && !screenshot.files.length) {
				event.preventDefault();
				transaction.setCustomValidity('Transaction ID या भुगतान स्क्रीनशॉट में से कम से कम एक देना आवश्यक है।');
				transaction.reportValidity();
			}
		});
	});
	var profilePicture = document.getElementById('member-profile-picture');
	if (profilePicture) { profilePicture.addEventListener('change', function () { var preview = document.querySelector('.member-profile-picture__preview'); if (!preview || !profilePicture.files.length) { return; } var image = document.createElement('img'); image.alt = ''; image.src = URL.createObjectURL(profilePicture.files[0]); image.onload = function () { URL.revokeObjectURL(image.src); }; preview.innerHTML = ''; preview.appendChild(image); }); }
}());
