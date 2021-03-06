<?php
/**
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

namespace App\Observers;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\Resume;

class ResumeObserver
{
	/**
	 * Listen to the Entry deleting event.
	 *
	 * @param Resume $resume
	 * @return void
	 */
	public function deleting(Resume $resume)
	{
		// Storage Disk Init.
		$pDisk = StorageDisk::getDisk('private');
		
		// Remove resume files (if exists)
		if (!empty($resume->filename)) {
			$filename = str_replace('uploads/', '', $resume->filename);
			
			if ($pDisk->exists($filename)) {
				$pDisk->delete($filename);
			}
		}
	}
	
	/**
	 * Listen to the Entry saved event.
	 *
	 * @param Resume $resume
	 * @return void
	 */
	public function saved(Resume $resume)
	{
		// Removing Entries from the Cache
		$this->clearCache($resume);
	}
	
	/**
	 * Listen to the Entry deleted event.
	 *
	 * @param Resume $resume
	 * @return void
	 */
	public function deleted(Resume $resume)
	{
		// Removing Entries from the Cache
		$this->clearCache($resume);
	}
	
	/**
	 * Removing the Entity's Entries from the Cache
	 *
	 * @param $resume
	 */
	private function clearCache($resume)
	{
		$limit = config('larapen.core.selectResumeInto', 5);
		
		try {
			cache()->forget('resumes.take.' . $limit . '.where.user.' . $resume->user_id);
			cache()->forget('resume.where.user.' . $resume->user_id);
		} catch (\Exception $e) {}
	}
}
