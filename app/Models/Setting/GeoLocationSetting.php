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

namespace App\Models\Setting;

class GeoLocationSetting
{
	public static function getValues($value, $disk)
	{
		if (empty($value)) {
			
			$value['show_country_flag'] = '1';
			
		} else {
			
			if (!isset($value['show_country_flag'])) {
				$value['show_country_flag'] = '1';
			}
			
		}
		
		return $value;
	}
	
	public static function setValues($value, $setting)
	{
		return $value;
	}
	
	public static function getFields($diskName)
	{
		$fields = [
			[
				'name'              => 'geolocation_activation',
				'label'             => trans('admin.Enable Geolocation'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.enable_geolocation_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'default_country_code',
				'label'             => trans('admin.Default Country'),
				'type'              => 'select2',
				'attribute'         => 'name',
				'model'             => '\\App\\Models\\Country',
				'allows_null'       => 'true',
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'  => 'separator_clear_1',
				'type'  => 'custom_html',
				'value' => '<div style="clear: both;"></div>',
			],
			[
				'name'              => 'show_country_flag',
				'label'             => trans('admin.show_country_flag_label'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.<br>'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
			[
				'name'              => 'local_currency_packages_activation',
				'label'             => trans('admin.Allow users to pay the Packages in their country currency'),
				'type'              => 'checkbox_switch',
				'hint'              => trans('admin.package_currency_by_country_hint'),
				'wrapperAttributes' => [
					'class' => 'col-md-6',
				],
			],
		];
		
		return $fields;
	}
}
