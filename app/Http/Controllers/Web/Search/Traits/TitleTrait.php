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

namespace App\Http\Controllers\Web\Search\Traits;

use App\Helpers\Search\PostQueries;
use App\Helpers\UrlGen;
use App\Http\Controllers\Web\Post\Traits\CatBreadcrumbTrait;
use App\Models\PostType;
use Illuminate\Support\Arr;

trait TitleTrait
{
	use CatBreadcrumbTrait;
	
	/**
	 * Get Search Meta Tags
	 *
	 * @return array
	 */
	public function getMetaTag(): array
	{
		$metaTag = [];
		
		[$title, $description, $keywords] = getMetaTag('search');
		
		$fallbackTitle = '';
		
		// Init.
		$fallbackTitle .= t('jobs_offers');
		
		// Keyword
		if (request()->filled('q')) {
			$fallbackTitle .= ' ' . t('for') . ' ';
			$fallbackTitle .= '"' . rawurldecode(request()->get('q')) . '"';
		}
		
		// Category
		if (isset($this->cat) && !empty($this->cat)) {
			[$title, $description, $keywords] = getMetaTag('searchCategory');
			
			// SubCategory
			if (isset($this->subCat) && !empty($this->subCat)) {
				$title = str_replace('{category.name}', $this->subCat->name, $title);
				$title = str_replace('{category.title}', $this->subCat->seo_title, $title);
				$description = str_replace('{category.name}', $this->subCat->name, $description);
				$description = str_replace('{category.description}', $this->subCat->seo_description, $description);
				$keywords = str_replace('{category.name}', mb_strtolower($this->subCat->name), $keywords);
				$keywords = str_replace('{category.keywords}', mb_strtolower($this->subCat->seo_keywords), $keywords);
				
				$fallbackTitle .= ' ' . $this->subCat->name . ',';
				if (!empty($this->subCat->seo_description)) {
					$fallbackDescription = $this->subCat->seo_description . ', ' . config('country.name');
				}
			} else {
				$title = str_replace('{category.name}', $this->cat->name, $title);
				$title = str_replace('{category.title}', $this->cat->seo_title, $title);
				$description = str_replace('{category.name}', $this->cat->name, $description);
				$description = str_replace('{category.description}', $this->cat->seo_description, $description);
				$keywords = str_replace('{category.name}', mb_strtolower($this->cat->name), $keywords);
				$keywords = str_replace('{category.keywords}', mb_strtolower($this->cat->seo_keywords), $keywords);
				
				$fallbackTitle .= ' ' . $this->cat->name;
				if (!empty($this->cat->seo_description)) {
					$fallbackDescription = $this->cat->seo_description . ', ' . config('country.name');
				}
			}
		}
		
		// User
		if (isset($this->sUser) && !empty($this->sUser)) {
			[$title, $description, $keywords] = getMetaTag('searchProfile');
			$title = str_replace('{profile.name}', $this->sUser->name, $title);
			$description = str_replace('{profile.name}', $this->sUser->name, $description);
			$keywords = str_replace('{profile.name}', mb_strtolower($this->sUser->name), $keywords);
			
			$fallbackTitle .= ' ' . t('of') . ' ';
			$fallbackTitle .= $this->sUser->name;
		}
		
		// Company
		if (isset($this->company) && !empty($this->company)) {
			[$title, $description, $keywords] = getMetaTag('searchProfile');
			$title = str_replace('{profile.name}', $this->company->name, $title);
			$description = str_replace('{profile.name}', $this->company->name, $description);
			$keywords = str_replace('{profile.name}', mb_strtolower($this->company->name), $keywords);
			
			$fallbackTitle .= ' ' . t('among') . ' ';
			$fallbackTitle .= $this->company->name;
		}
		
		// Tag
		if (isset($this->tag) && !empty($this->tag)) {
			[$title, $description, $keywords] = getMetaTag('searchTag');
			$title = str_replace('{tag}', $this->tag, $title);
			$description = str_replace('{tag}', $this->tag, $description);
			$keywords = str_replace('{tag}', mb_strtolower($this->tag), $keywords);
			
			$fallbackTitle .= ' ' . t('for') . ' ';
			$fallbackTitle .= $this->tag . ' (' . t('Tag') . ')';
		}
		
		// Location
		if (request()->filled('r') && !request()->filled('l')) {
			// Administrative Division
			if (isset($this->admin) && !empty($this->admin)) {
				[$title, $description, $keywords] = getMetaTag('searchLocation');
				$title = str_replace('{location.name}', $this->admin->name, $title);
				$description = str_replace('{location.name}', $this->admin->name, $description);
				$keywords = str_replace('{location.name}', mb_strtolower($this->admin->name), $keywords);
				
				$fallbackTitle .= ' ' . t('in') . ' ';
				$fallbackTitle .= $this->admin->name;
				$fallbackDescription = t('ads_in_location', ['location' => $this->admin->name])
					. ', ' . config('country.name')
					. '. ' . t('Looking for a job')
					. ' - ' . $this->admin->name
					. ', ' . config('country.name');
			}
		} else {
			// City
			if (isset($this->city) && !empty($this->city)) {
				[$title, $description, $keywords] = getMetaTag('searchLocation');
				$title = str_replace('{location.name}', $this->city->name, $title);
				$description = str_replace('{location.name}', $this->city->name, $description);
				$keywords = str_replace('{location.name}', mb_strtolower($this->city->name), $keywords);
				
				$fallbackTitle .= ' ' . t('in') . ' ';
				$fallbackTitle .= $this->city->name;
				$fallbackDescription = t('ads_in_location', ['location' => $this->city->name])
					. ', ' . config('country.name')
					. '. ' . t('Looking for a job')
					. ' - ' . $this->city->name
					. ', ' . config('country.name');
			}
		}
		
		// Country
		$fallbackTitle .= ', ' . config('country.name');
		
		// view()->share('title', $fallbackTitle);
		
		$title = replaceGlobalPatterns($title);
		$description = replaceGlobalPatterns($description);
		$keywords = mb_strtolower(replaceGlobalPatterns($keywords));
		
		$metaTag['title'] = !empty($title) ? $title : $fallbackTitle;
		$metaTag['description'] = !empty($description) ? $description : ($fallbackDescription ?? $fallbackTitle);
		$metaTag['keywords'] = $keywords;
		
		return array_values($metaTag);
	}
	
	/**
	 * Get Search HTML Title
	 *
	 * @return string
	 */
	public function getHtmlTitle(): string
	{
		// Title
		$htmlTitle = '';
		
		// Init.
		$htmlTitle .= t('All jobs');
		
		// Location
		$searchUrl = UrlGen::search([], ['l', 'r', 'location', 'distance']);
		
		if (request()->filled('r') && !request()->filled('l')) {
			// Administrative Division
			if (isset($this->admin) && !empty($this->admin)) {
				$htmlTitle .= ' ' . t('in') . ' ';
				$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
				$htmlTitle .= $this->admin->name;
				$htmlTitle .= '</a>';
			}
		} else {
			// City
			if (isset($this->city) && !empty($this->city)) {
				if (config('settings.list.cities_extended_searches')) {
					$htmlTitle .= ' ' . t('within') . ' ';
					$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
					$htmlTitle .= t('x_distance_around_city', [
						'distance' => (PostQueries::$distance == 1) ? 0 : PostQueries::$distance,
						'unit'     => getDistanceUnit(config('country.code')),
						'city'     => $this->city->name,
					]);
					$htmlTitle .= '</a>';
				} else {
					$htmlTitle .= ' ' . t('in') . ' ';
					$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
					$htmlTitle .= $this->city->name;
					$htmlTitle .= '</a>';
				}
			}
		}
		
		// Category
		if (isset($this->cat) && !empty($this->cat)) {
			// Get the parent of parent category URL
			$exceptArr = ['c', 'sc', 'cf', 'minPrice', 'maxPrice'];
			$searchUrl = UrlGen::getCatParentUrl($this->cat->parent->parent ?? null, $this->city ?? null, $exceptArr);
			
			if (isset($this->subCat) && !empty($this->subCat)) {
				$htmlTitle .= ' ' . t('in') . ' ';
				$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
				$htmlTitle .= $this->subCat->name;
				$htmlTitle .= '</a>';
				
				// Get the parent category URL
				$exceptArr = ['sc', 'cf', 'minPrice', 'maxPrice'];
				$searchUrl = UrlGen::getCatParentUrl($this->cat->parent ?? null, $this->city ?? null, $exceptArr);
			}
			
			$htmlTitle .= ' ' . t('in') . ' ';
			$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
			$htmlTitle .= $this->cat->name;
			$htmlTitle .= '</a>';
		}
		
		// Company
		if (isset($this->company) && !empty($this->company)) {
			$htmlTitle .= ' ' . t('among') . ' ';
			$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . UrlGen::search() . '">';
			$htmlTitle .= $this->company->name;
			$htmlTitle .= '</a>';
		}
		
		// Tag
		if (isset($this->tag) && !empty($this->tag)) {
			$htmlTitle .= ' ' . t('for') . ' ';
			$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . UrlGen::search() . '">';
			$htmlTitle .= $this->tag;
			$htmlTitle .= '</a>';
		}
		
		// Date
		if (request()->filled('postedDate') && isset($this->dates) && isset($this->dates->{request()->get('postedDate')})) {
			$exceptArr = ['postedDate'];
			$searchUrl = UrlGen::search([], $exceptArr);
			
			$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
			$htmlTitle .= $this->dates->{request()->get('postedDate')};
			$htmlTitle .= '</a>';
		}
		
		// Job Type
		if (request()->filled('type')) {
			if (is_array(request()->get('type'))) {
				foreach (request()->get('type') as $key => $value) {
					$jobType = PostType::find($value);
					if (!empty($jobType)) {
						$exceptArr = ['type.' . $key];
						$searchUrl = UrlGen::search([], $exceptArr);
						
						$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
						$htmlTitle .= $jobType->name;
						$htmlTitle .= '</a>';
					}
				}
			} else {
				$jobType = PostType::find(request()->get('type'));
				if (!empty($jobType)) {
					$exceptArr = ['type'];
					$searchUrl = UrlGen::search([], $exceptArr);
					
					$htmlTitle .= '<a rel="nofollow" class="jobs-s-tag" href="' . $searchUrl . '">';
					$htmlTitle .= $jobType->name;
					$htmlTitle .= '</a>';
				}
			}
		}
		
		view()->share('htmlTitle', $htmlTitle);
		
		return $htmlTitle;
	}
	
	/**
	 * Get Breadcrumbs Tabs
	 *
	 * @return array
	 */
	public function getBreadcrumb(): array
	{
		$bcTab = [];
		
		// City
		if (isset($this->city) && !empty($this->city)) {
			$title = t('in_x_distance_around_city', [
				'distance' => (PostQueries::$distance == 1) ? 0 : PostQueries::$distance,
				'unit'     => getDistanceUnit(config('country.code')),
				'city'     => $this->city->name,
			]);
			
			$bcTab[] = collect([
				'name'     => (isset($this->cat) ? t('All jobs') . ' ' . $title : $this->city->name),
				'url'      => UrlGen::city($this->city),
				'position' => (isset($this->cat) ? 5 : 3),
				'location' => true,
			]);
		}
		
		// Admin
		if (isset($this->admin) && !empty($this->admin)) {
			$queryArr = [
				'd' => config('country.icode'),
				'r' => $this->admin->name
			];
			$exceptArr = ['l', 'location', 'distance'];
			$searchUrl = UrlGen::search($queryArr, $exceptArr);
			
			$title = $this->admin->name;
			
			$bcTab[] = collect([
				'name'     => (isset($this->cat) ? t('All jobs') . ' ' . $title : $this->admin->name),
				'url'      => $searchUrl,
				'position' => (isset($this->cat) ? 5 : 3),
				'location' => true,
			]);
		}
		
		// Category
		$catBreadcrumb = $this->getCatBreadcrumb($this->cat, 3);
		$bcTab = array_merge($bcTab, $catBreadcrumb);
		
		// Company
		if (isset($this->company) && !empty($this->company)) {
			$bcTab[] = collect([
				'name'     => $this->company->name,
				'url'      => UrlGen::company(null, $this->company->id),
				'position' => (isset($this->cat) ? 5 : 3),
				'location' => true,
			]);
		}
		
		// Sort by Position
		$bcTab = array_values(Arr::sort($bcTab, function ($value) {
			return $value->get('position');
		}));
		
		view()->share('bcTab', $bcTab);
		
		return $bcTab;
	}
}
