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
	
	/* Save the Post */
	$('.make-favorite, .save-job, a.saved-job').click(function () {
		if (isLogged !== true) {
			openLoginModal();
			
			return false;
		}
		
		savePost(this);
	});
	
	/* Save the Search */
	$('#saveSearch').click(function () {
		if (isLogged !== true) {
			openLoginModal();
			
			return false;
		}
		
		saveSearch(this);
	});
	
});

/**
 * Save Ad
 * @param elmt
 * @returns {boolean}
 */
function savePost(elmt) {
	var postId = $(elmt).closest('li').attr('id');
	
	let url = siteUrl + '/ajax/save/post';
	
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: {
			'postId': postId,
			'_token': $('input[name=_token]').val()
		}
	});
	ajax.done(function (xhr) {
		/* console.log(xhr); */
		if (typeof xhr.isLogged === 'undefined') {
			return false;
		}
		
		if (xhr.isLogged !== true) {
			openLoginModal();
			
			return false;
		}
		
		/* Logged Users - Notification */
		if (xhr.status == 1) {
			if ($(elmt).hasClass('btn')) {
				$('#' + xhr.postId).removeClass('saved-job').addClass('saved-job');
				$('#' + xhr.postId + ' a').removeClass('save-job').addClass('saved-job');
			} else {
				$(elmt).html('<span class="fas fa-bookmark"></span> ' + lang.labelSavePostRemove);
			}
			
			jsAlert(lang.confirmationSavePost, 'success');
		} else {
			if ($(elmt).hasClass('btn')) {
				$('#' + xhr.postId).removeClass('save-job').addClass('save-job');
				$('#' + xhr.postId + ' a').removeClass('saved-job').addClass('save-job');
			} else {
				$(elmt).html('<span class="far fa-bookmark"></span> ' + lang.labelSavePostSave);
			}
			
			jsAlert(lang.confirmationRemoveSavePost, 'success');
		}
		
		return false;
	});
	ajax.fail(function (xhr, textStatus, errorThrown) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error', false);
		}
	});
	
	return false;
}

/**
 * Save Search
 * @param elmt
 * @returns {boolean}
 */
function saveSearch(elmt) {
	var searchUrl = $(elmt).attr('name');
	var countPosts = $(elmt).attr('count');
	
	let url = siteUrl + '/ajax/save/search';
	
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: {
			'url': searchUrl,
			'countPosts': countPosts,
			'_token': $('input[name=_token]').val()
		}
	});
	ajax.done(function (xhr) {
		/* console.log(xhr); */
		if (typeof xhr.isLogged === 'undefined') {
			return false;
		}
		
		if (xhr.isLogged !== true) {
			openLoginModal();
			
			return false;
		}
		
		/* Logged Users - Notification */
		let message = lang.confirmationRemoveSaveSearch;
		if (xhr.status === 1) {
			message = lang.confirmationSaveSearch;
		}
		
		jsAlert(message, 'success');
		
		return false;
	});
	ajax.fail(function (xhr, textStatus, errorThrown) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error', false);
		}
	});
	
	return false;
}
