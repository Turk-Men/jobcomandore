<?php
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

namespace App\Listeners;

use App\Events\PostWasVisited;

class UpdateThePostCounter
{
	/**
	 * Create the event listener.
	 */
	public function __construct()
	{
		//
	}
	
	/**
	 * Handle the event.
	 *
	 * @param \App\Events\PostWasVisited $event
	 * @return bool
	 */
	public function handle(PostWasVisited $event)
	{
		// Don't count the self-visits
		if (auth()->check()) {
			if (auth()->user()->id == $event->post->user_id) {
				return false;
			}
		}
		
		if (!session()->has('postIsVisited')) {
			return $this->updateCounter($event->post);
		} else {
			if (session()->get('postIsVisited') != $event->post->id) {
				return $this->updateCounter($event->post);
			} else {
				return false;
			}
		}
	}
	
	/**
	 * @param $post
	 * @return bool
	 */
	public function updateCounter($post): bool
	{
		try {
			$post->visits = $post->visits + 1;
			$post->save(['canBeSaved' => true]);
			session()->put('postIsVisited', $post->id);
		} catch (\Throwable $e) {
			return false;
		}
		
		return true;
	}
}
