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

namespace Foomo\Site\Utils;

use Foomo\Site;
use Foomo\ContentServer\Vo;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Uri
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Checks for locale pattern like:
	 *
	 * /ch-fr/some/path
	 * /ch-fr/
	 * /ch-fr
	 * /ch/some/path
	 * /ch/
	 * /ch
	 * /
	 *
	 * and returns an array of region, language, and path without the locale
	 *
	 * @param string $uri
	 * @return array
	 */
	public static function parseLocale($uri)
	{
		$config = Site::getConfig();
		$session = Site::getSession();

		# parse locale
		if (
			preg_match('/^\/(?P<region>[a-z]{2})-(?P<language>[a-z]{2})(\/|$)/', $uri, $matches) &&
			$config->isValidRegion($matches['region']) &&
			$config->isValidLanguage($matches['region'], $matches['language'])
		) {
			$region = $matches['region'];
			$language = $matches['language'];
			$path = substr($uri, 0, 6);
			$uri = substr($uri, 6);
		} else if (
			preg_match('/^\/(?P<region>[a-z]{2})(\/|$)/', $uri, $matches) &&
			$config->isValidRegion($matches['region'])
		) {
			$region = $matches['region'];
			$language = $config->getDefaultLanguage($matches['region']);
			$path = substr($uri, 0, 3);
			$uri = substr($uri, 3);
		} else {
			$region = $session::getRegion();
			$language = $session::getLanguage();
			$path = '';
		}
		return compact('region', 'language', 'path', 'uri');
	}

	/**
	 * @param string $uri
	 * @return string
	 */
	public static function shortenLocale($uri)
	{
		$config = Site::getConfig();
		$locale = self::parseLocale($uri);

		if (
			$locale['region'] == $config->getDefaultRegion() &&
			$locale['language'] == $config->getDefaultLanguage($locale['region'])
		) {
			return $locale['uri'];
		} else if (
			$locale['region'] != $config->getDefaultRegion() &&
			$locale['language'] == $config->getDefaultLanguage($locale['region'])
		) {
			return '/' . $locale['region'] . $locale['uri'];
		} else {
			return '/' . $locale['region'] . '-' . $locale['language'] . $locale['uri'];
		}
	}
}
