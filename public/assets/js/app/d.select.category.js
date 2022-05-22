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

/* Prevent errors, If these variables are missing. */
if (typeof packageIsEnabled === 'undefined') {
	var packageIsEnabled = false;
}

$(document).ready(function () {
	
	/* On load */
	var catId = $('#categoryId').val();
	getCategories(siteUrl, languageCode, catId);
	
	/* On category selected */
	$(document).on('click', '.cat-link', function (e) {
		e.preventDefault(); /* prevents the submit or reload */
		
		catId = $(this).data('id');
		if (typeof catId === 'undefined') {
			catId = 0;
		}
		getCategories(siteUrl, languageCode, catId);
	});
	
});

function getCategories(siteUrl, languageCode, catId) {
	let url = siteUrl + '/ajax/category/select-category';
	
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: {
			'_token': $('input[name=_token]').val(),
			'catId': catId
		}
	});
	ajax.done(function (xhr) {
		if (typeof xhr.html === 'undefined' || typeof xhr.hasChildren === 'undefined') {
			return false;
		}
		
		/* Get & append the category's children */
		if (xhr.hasChildren) {
			$('#selectCats').html(xhr.html);
		} else {
			/* Select the category & append it */
			$('#catsContainer').html(xhr.html);
			
			if (typeof xhr.category === 'undefined' || typeof xhr.category.id === 'undefined') {
				return false;
			}
			
			/* Save data in hidden field */
			$('#categoryId').val(xhr.category.id);
			$('#categoryType').val(xhr.category.type);
			
			/* Close the Modal */
			/* $('#browseCategories').modal('hide'); */
			let modalEl = document.querySelector('#browseCategories');
			if (modalEl !== null) {
				let modalObj = bootstrap.Modal.getInstance(modalEl);
				modalObj.hide();
			}
		}
	});
	ajax.fail(function(xhr) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error', false, true);
			
			/* Close the Modal */
			let modalEl = document.querySelector('#browseCategories');
			if (modalEl !== null) {
				let modalObj = bootstrap.Modal.getInstance(modalEl);
				modalObj.hide();
			}
		}
	});
}
