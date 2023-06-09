/**
 * Lockout plugin for Craft CMS 4.x
 *
 * LockoutWidget JS
 *
 * @author    Jalen Davenport
 * @copyright Copyright (c) 2019 Jalen Davenport
 * @link      https://jalendport.com
 * @package   Lockout
 * @since     1.0.0
 */

var iconContainer = document.querySelector('div.jalendport\\\\lockout\\\\widgets\\\\lockoutwidget div.lockout-icon-container');
var icon = iconContainer.querySelector('svg.lockout-icon');

icon.addEventListener("click", function(){
	if (iconContainer.classList.contains('disabled')) {
		Craft.sendActionRequest('POST', 'lockout/enable', {})
			.then((response) => {
				location.reload();
			});
	} else if (iconContainer.classList.contains('enabled')) {
		Craft.sendActionRequest('POST', 'lockout/disable', {})
			.then((response) => {
				location.reload();
			});
	}
});
