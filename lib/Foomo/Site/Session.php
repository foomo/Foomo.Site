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
 * @author  franklin
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
	/**
	 * @var string[]
	 */
	private $groups = [];
	/**
	 * @var string
	 */
	private $state;

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		# set default values
		$config = Module::getSiteConfig();
		$this->region = $config->getDefaultRegion();
		$this->language = $config->getDefaultLanguage();
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function boot()
	{
		static::getInstance();
		static::updateLocaleChain();
	}

	/**
	 * @inheritdoc
	 *
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
		if ($config->isValidRegion($region)) {
			static::getInstance(true)->region = $region;
			// check if language exists
			if (!$config->isValidLanguage($region, static::getLanguage())) {
				static::setLanguage($config->getDefaultLanguage($region));
			}
			static::updateLocaleChain();
		} else {
			trigger_error("Invalid region: $region", E_USER_WARNING);
		}
	}

	/**
	 * @inheritdoc
	 *
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
		if ($language != static::getLanguage()) {
			$config = Module::getSiteConfig();

			if ($config->isValidLanguage(static::getRegion(), $language)) {
				static::getInstance(true)->language = $language;
				static::updateLocaleChain();
			} else {
				trigger_error("Invalid language: $language", E_USER_WARNING);
			}
		}
	}

	/**
	 * @inheritdoc
	 *
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

	/**
	 * @inheritdoc
	 *
	 * @return string[]
	 */
	public static function getGroups()
	{
		return static::getInstance()->groups;
	}

	/**
	 * @param string[] $groups
	 */
	public static function setGroups($groups)
	{
		if (count(array_diff($groups, static::getGroups())) > 0) {
			$config = Module::getSiteConfig();
			if ($config->areValidGroups($groups)) {
				static::getInstance(true)->groups = array_unique($groups);
			} else {
				trigger_error("Invalid groups: " . join(', ', $groups), E_USER_WARNING);
			}
		}
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public static function getState()
	{
		return static::getInstance()->state;
	}

	/**
	 * @param string $state
	 */
	public static function setState($state)
	{
		if ($state != static::getState()) {
			$config = Module::getSiteConfig();
			if ($config->isValidState($state)) {
				static::getInstance(true)->state = $state;
			} else {
				trigger_error("Invalid state: $state", E_USER_WARNING);
			}
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $write return session in write mode
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
		Translation::setDefaultLocaleChain([static::getLocale(), static::getLanguage()]);
	}
}
