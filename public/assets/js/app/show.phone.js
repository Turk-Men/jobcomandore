/*
 * JobClass - Job Board Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com/jobclass
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

$(document).ready(function () {
	
	$('.phoneBlock').click(function (e) {
		e.preventDefault(); /* prevents the submit or reload */
		
		showPhone();
		
		return false;
	});
	
});

/**
 * Show the Contact's phone
 * @returns {boolean}
 */
function showPhone() {
	let postId = $('#postId').val();
	
	if (postId === 0 || postId === '0' || postId === '') {
		return false;
	}
	
	let resultCanBeCached = true;
	let url = siteUrl + '/ajax/post/phone';
	
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: {
			'postId': postId,
			'_token': $('input[name=_token]').val()
		},
		cache: resultCanBeCached
	});
	ajax.done(function (xhr) {
		if (typeof xhr.phone == 'undefined') {
			return false;
		}
		
		let phoneBlockEl = $('.phoneBlock');
		
		phoneBlockEl.html('<i class="fas fa-mobile-alt"></i> ' + xhr.phone);
		phoneBlockEl.attr('href', xhr.link);
		phoneBlockEl.tooltip('dispose'); /* Disable Tooltip */
		
		if (resultCanBeCached) {
			$('#postId').val(0);
		}
	});
	ajax.fail(function(xhr) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error');
		}
	});
}
