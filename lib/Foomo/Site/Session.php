<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site;

use Foomo\Translation;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Session implements SessionInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	private $region;
	/**
	 * @var string
	 */
	private $language;

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		$config = Module::getSiteConfig();

		# set default values
		$territories = array_keys($config->locales);
		$this->region = $territories[0];
		$this->language = $config->locales[$this->region][0];
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Start session
	 */
	public static function boot()
	{
		static::getInstance();
	}

	/**
	 * @return string
	 */
	public static function getRegion()
	{
		return static::getInstance()->region;
	}

	/**
	 * @param string $region
	 */
	public static function setRegion($region)
	{
		$config = Module::getSiteConfig();
		if (
			$region != static::getRegion() &&
			isset($config->locales[$region])
		) {
			static::getInstance(true)->region = $region;
			// check if language exists
			if (!in_array($config->locales[$region], static::getLanguage())) {
				static::setLanguage($config->locales[$region][0]);
			}
			static::updateLocaleChain();
		}
	}

	/**
	 * @return string
	 */
	public static function getLanguage()
	{
		return static::getInstance()->language;
	}

	/**
	 * @param string $language
	 */
	public static function setLanguage($language)
	{
		$config = Module::getSiteConfig();
		if (
			$language != static::getLanguage() &&
			in_array($config->locales[static::getRegion()], $language)
		) {
			static::getInstance(true)->language = $language;
			static::updateLocaleChain();
		}
	}

	/**
	 * @return string
	 */
	public static function getLocale()
	{
		return strtolower(static::getRegion()) . '_' . strtoupper(static::getLanguage());
	}

	/**
	 * @param string $region
	 * @param string $language
	 * @return string
	 */
	public static function setLocale($region, $language)
	{
		static::setRegion($region);
		static::setLanguage($language);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $write
	 * @return static
	 */
	protected static function getInstance($write = false)
	{
		if ($write) {
			\Foomo\Session::lockAndLoad();
		}
		return \Foomo\Session::getClassInstance(get_called_class());
	}

	/**
	 * Update the default translation locale chain
	 */
	protected static function updateLocaleChain()
	{
		Translation::setDefaultLocaleChain([static::getLocale()]);
	}
}
