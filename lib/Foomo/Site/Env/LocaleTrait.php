<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site\Env;

use Foomo\Site;
use Foomo\Translation;

/**
 * Using this trait expects you to configure all your dimensions with a language & region property i.e.
 *
 * dimensions:
 * 	 en_US:
 * 		 region: us
 * 	   language: en
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
trait LocaleTrait
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $region;
	/**
	 * @var string
	 */
	protected $language;

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Current region i.e. `us`
	 *
	 * @return string
	 */
	public static function getRegion()
	{
		return static::getInstance()->region;
	}

	/**
	 * Set the current dimension to a configured region while trying to keep the language
	 *
	 * @param string $region
	 */
	public static function setRegion($region)
	{
		$config = Site::getConfig();
		$inst = static::getInstance();
		# get all dimension with the given region
		$regionDimensions = $config->findDimensionsWithValue('region', $region);
		# validate
		if (empty($regionDimensions)) {
			trigger_error("Invalid region: $region", E_USER_ERROR);
		}
		# default region dimension
		$dimension = reset($regionDimensions);
		# try to keep current language
		foreach ($regionDimensions as $regionDimension) {
			if ($regionDimension['language'] == $inst->language) {
				$dimension = $regionDimension;
				break;
			}
		}
		# update locale
		static::setLocale($dimension['region'], $dimension['language']);
	}

	/**
	 * Current language i.e. `en`
	 *
	 * @return string
	 */
	public static function getLanguage()
	{
		return static::getInstance()->language;
	}

	/**
	 * Set the current dimension to a configured language while trying to keep the region
	 *
	 * @param string $language
	 */
	public static function setLanguage($language)
	{
		$config = Site::getConfig();
		$inst = static::getInstance();
		# get all dimension with the given language
		$languageDimensions = $config->findDimensionsWithValue('language', $language);
		# validate
		if (empty($languageDimensions)) {
			trigger_error("Invalid language: $language", E_USER_ERROR);
		}
		$dimension = reset($languageDimensions);
		# try to keep current region
		foreach ($languageDimensions as $languageDimension) {
			if ($languageDimension['region'] == $inst->region) {
				$dimension = $languageDimension;
				break;
			}
		}
		# update locale
		static::setLocale($dimension['region'], $dimension['language']);
	}

	/**
	 * Current locale i.e. `en_US`
	 *
	 * @return string|null
	 */
	public static function getLocale()
	{
		if (!static::getLanguage() || !static::getRegion()) {
			return null;
		} else {
			return strtolower(static::getLanguage()) . '_' . strtoupper(static::getRegion());
		}
	}

	/**
	 * @param string $region
	 * @param string $language
	 * @return string
	 */
	public static function setLocale($region, $language)
	{
		if (
			static::getRegion() != $region ||
			static::getLanguage() != $language
		) {
			/* @var $inst self */
			$inst = static::getInstance(true);
			$inst->region = $region;
			$inst->language = $language;
		}
		static::updateLocaleChain();
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Update the default translation locale chain
	 */
	protected static function updateLocaleChain()
	{
		$locale = static::getLocale();
		$language = static::getLanguage();
		Translation::setDefaultLocaleChain([$locale, $language]);
	}
}
