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
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Session
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	private $language;
	/**
	 * @var string
	 */
	private $region;

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
		$territories = array_keys($config->allowedLocales);
		$this->region = $territories[0];
		$this->language = $config->allowedLocales[$this->region][0];
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public static function boot()
	{
		self::getInstance();
	}

	/**
	 * @return string
	 */
	public static function getLanguage()
	{
		return self::getInstance()->language;
	}
	/**
	 * @param $language
	 */
	public static function setLanguage($language)
	{
		$config = Module::getSiteConfig();
		if (
				$language != self::getLanguage() &&
				in_array($config->allowedLocales[self::getRegion()], $language)
		) {
			self::getInstance(true)->language = $language;
			self::updateLocaleChain();
		}
	}

	/**
	 * @return string
	 */
	public static function getRegion()
	{
		return self::getInstance()->region;
	}
	/**
	 * @param $region
	 */
	public static function setRegion($region)
	{
		$config = Module::getSiteConfig();
		if (
			$region != self::getRegion() &&
			isset($config->allowedLocales[$region])
		) {
			self::getInstance(true)->region = $region;
			// check if language exists
			if (!in_array($config->allowedLocales[$region], self::getLanguage())) {
				self::setLanguage($config->allowedLocales[$region][0]);
			}
			self::updateLocaleChain();
		}
	}

	/**
	 * @return string
	 */
	public static function getLocale()
	{
		return self::getRegion() . '_' . self::getLanguage();
	}
	/**
	 * @param $region
	 * @param $language
	 * @return string
	 */
	public static function setLocale($region, $language)
	{
		self::setRegion($region);
		self::setLanguage($language);
	}

	/**
	 * @return string
	 */
	public static function getProtocol()
	{
		return self::getRegion() . '_' . self::getLanguage();
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $write
	 * @return self
	 */
	private static function getInstance($write=false)
	{
		if ($write) {
			\Foomo\Session::lockAndLoad();
		}
		return \Foomo\Session::getClassInstance(__CLASS__);
	}

	/**
	 *
	 */
	private static function updateLocaleChain()
	{
		Translation::setDefaultLocaleChain([self::getLocale()]);
	}
}