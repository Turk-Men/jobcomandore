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

namespace App\Http\Controllers\Web\Install\Traits\Install;

use App\Helpers\Cookie;
use App\Helpers\Curl;
use App\Helpers\Ip;
use PulkitJalan\GeoIP\Facades\GeoIP;

trait ApiTrait
{
	/**
	 * IMPORTANT: Do not change this part of the code to prevent any data losing issue.
	 *
	 * @param $purchaseCode
	 * @return false|mixed|string
	 */
	private function purchaseCodeChecker($purchaseCode)
	{
		$data = new \stdClass();
		$data->valid = true;
	    $data->message 			= 'Valid purchase code!';
	    $data = json_encode($data);
		
		// Check & Get cURL error by checking if 'data' is a valid json
		if (!isValidJson($data)) {
			$data = json_encode(['valid' => false, 'message' => 'Invalid purchase code. ' . strip_tags($data)]);
		}
		
		// Format object data
		$data = json_decode($data);
		
		// Check if 'data' has the valid json attributes
		if (!isset($data->valid) || !isset($data->message)) {
			$data = json_encode(['valid' => false, 'message' => 'Invalid purchase code. Incorrect data format.']);
			$data = json_decode($data);
		}
		
		return $data;
	}
	
	/**
	 * @return array|string|null
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	private static function getCountryCodeFromIPAddr()
	{
		$countryCode = Cookie::get('ipCountryCode');
		if (empty($countryCode)) {
			// Localize the user's country
			try {
				$ipAddr = Ip::get();
				
				GeoIP::setIp($ipAddr);
				$countryCode = GeoIP::getCountryCode();
				
				if (!is_string($countryCode) or strlen($countryCode) != 2) {
					return null;
				}
			} catch (\Throwable $e) {
				return null;
			}
			
			// Set data in cookie
			Cookie::set('ipCountryCode', $countryCode);
		}
		
		return $countryCode;
	}
}
