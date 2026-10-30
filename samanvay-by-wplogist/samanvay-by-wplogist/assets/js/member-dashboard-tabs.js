(function () {
	'use strict';
	var bar = document.querySelector('.member-dashboard-tabs');
	if (!bar) { return; }
	var tabs = Array.from(bar.querySelectorAll('[data-dashboard-tab]'));
	var panels = Array.from(document.querySelectorAll('[data-dashboard-panel]'));
	bar.hidden = false;
	bar.setAttribute('role', 'tablist');
	tabs.forEach(function (tab) {
		tab.setAttribute('role', 'tab');
		tab.setAttribute('aria-controls', 'dashboard-panel-' + tab.dataset.dashboardTab);
	});
	panels.forEach(function (panel) {
		panel.setAttribute('role', 'tabpanel');
		panel.setAttribute('aria-labelledby', 'dashboard-tab-' + panel.dataset.dashboardPanel);
		panel.tabIndex = 0;
	});
	function select(name) {
		if (!tabs.some(function (tab) { return tab.dataset.dashboardTab === name; })) { name = 'profile'; }
		tabs.forEach(function (tab) {
			var active = tab.dataset.dashboardTab === name;
			tab.setAttribute('aria-selected', String(active));
			tab.tabIndex = active ? 0 : -1;
		});
		panels.forEach(function (panel) {
			panel.hidden = panel.dataset.dashboardPanel !== name;
			// Hidden panels must not remain invisible due to scroll-reveal timing.
			if (!panel.hidden) { panel.querySelectorAll('.sam-reveal').forEach(function (el) { el.classList.add('is-revealed'); }); }
		});
	}
	tabs.forEach(function (tab, index) {
		tab.addEventListener('click', function () { select(tab.dataset.dashboardTab); });
		tab.addEventListener('keydown', function (event) {
			var next;
			if (event.key === 'ArrowRight') { next = (index + 1) % tabs.length; }
			else if (event.key === 'ArrowLeft') { next = (index + tabs.length - 1) % tabs.length; }
			else if (event.key === 'Home') { next = 0; }
			else if (event.key === 'End') { next = tabs.length - 1; }
			else { return; }
			event.preventDefault(); select(tabs[next].dataset.dashboardTab); tabs[next].focus();
		});
	});
	var params = new URLSearchParams(window.location.search);
	var error = params.get('dashboard_error') || '';
	var name = 'profile';
	if (params.has('password_updated') || ['current_password', 'new_password', 'password_unchanged', 'password_update_failed'].includes(error)) { name = 'password'; }
	else if (params.has('payment_submitted') || ['payment_proof_required', 'invalid_transaction', 'duplicate_transaction', 'invalid_file', 'upload_failed'].includes(error)) { name = 'payments'; }
	function hashTab() {
		var target = document.getElementById(window.location.hash.slice(1));
		var panel = target && target.closest('[data-dashboard-panel]');
		if (panel) { select(panel.dataset.dashboardPanel); return true; }
		return false;
	}
	select(name);
	hashTab();
	window.addEventListener('hashchange', hashTab);
}());
