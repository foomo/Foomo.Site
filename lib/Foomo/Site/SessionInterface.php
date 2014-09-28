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

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
interface SessionInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Start session
	 */
	static function boot();

	/**
	 * Current language i.e. `en`
	 *
	 * @return string
	 */
	static function getLanguage();

	/**
	 * Current region i.e. `us`
	 *
	 * @return string
	 */
	static function getRegion();

	/**
	 * @param string $region
	 */
	static function setRegion($region);

	/**
	 * Current locale i.e. `en_US`
	 *
	 * @return string
	 */
	static function getLocale();

	/**
	 * @param string $language
	 */
	static function setLanguage($language);

	/**
	 * @param string $region
	 * @param string $language
	 * @return string
	 */
	static function setLocale($region, $language);

	/**
	 * Current groups i.e. `['www', 'registered']`
	 *
	 * @return string[]
	 */
	static function getGroups();

	/**
	 * Current state i.e. `debug`
	 *
	 * @return string
	 */
	static function getState();
}
