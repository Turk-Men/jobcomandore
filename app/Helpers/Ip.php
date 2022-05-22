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

namespace App\Helpers;

class Ip
{
	/**
	 * Get user's IP address
	 *
	 * @param bool $canGetLocalIp
	 * @param string $defaultIp
	 * @return string
	 *
	 * e.g. '157.240.1.35' (US) - '197.234.219.43' (BJ) - '5.135.32.116' (FR) => For debug | @todo: remove debug value
	 */
	public static function get(bool $canGetLocalIp = true, string $defaultIp = '157.240.1.35'): string
	{
		$ip = '';
		
		$serverVarKeys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];
		
		foreach ($serverVarKeys as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $item) {
					if (
						self::isValid($item) &&
						substr($item, 0, 4) != '127.' &&
						$item != '::1' &&
						$item != '' &&
						!in_array($item, ['255.255.255.0', '255.255.255.255'])
					) {
						$ip = $item;
						break;
					}
				}
			}
		}
		
		if ($canGetLocalIp && empty($ip)) {
			foreach ($serverVarKeys as $key) {
				if (array_key_exists($key, $_SERVER) === true) {
					foreach (explode(',', $_SERVER[$key]) as $item) {
						if (self::isValid($item) && $item != '') {
							$ip = $item;
							break;
						}
					}
				}
			}
		}
		
		return ($ip) ? $ip : $defaultIp;
	}
	
	/**
	 * Check IP address version
	 *
	 * @param string $ip
	 * @return int
	 */
	public static function version(string $ip): int
	{
		return strpos($ip, ":") === false ? 4 : 6;
	}
	
	/**
	 * Validate an IP address
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValid(string $ip): bool
	{
		return (bool)filter_var($ip, FILTER_VALIDATE_IP);
	}
	
	/**
	 * Validate an IPv4 IP address
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValidIPv4(string $ip): bool
	{
		return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}
	
	/**
	 * Validate an IPv4 IP address
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValidIPv4RegEx(string $ip): bool
	{
		return (bool)preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip);
	}
	
	/**
	 * Validate an IPv4 IP address
	 * excluding private range addresses
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValidIPv4NoPriv(string $ip): bool
	{
		return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE);
	}
	
	/**
	 * Validate an IPv6 IP address
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValidIPv6(string $ip): bool
	{
		return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}
	
	/**
	 * Validate an IPv6 IP address
	 * excluding private range addresses
	 *
	 * @param string $ip
	 * @return bool
	 */
	public static function isValidIPv6NoPriv(string $ip): bool
	{
		return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE);
	}
	
	/**
	 * Convert ip address to ip number
	 *
	 * @param string $ip
	 * @return float|int|mixed|string
	 */
	public static function ipToNumber(string $ip)
	{
		if (trim($ip) == '') {
			return 0;
		} else {
			$tmp = preg_split("#\.#", $ip);
			
			return ($tmp[3] + $tmp[2] * 256 + $tmp[1] * 256 * 256 + $tmp[0] * 256 * 256 * 256);
		}
	}
}
