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

namespace Foomo\Site\Adapter;

use Foomo\Site\Module;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Cache
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the local filename for the given source node id and url
	 *
	 * @param string $nodeId Source node id
	 * @param string $url Source url
	 * @param string $type Source type
	 * @param int $time Last modification timestamp
	 * @return bool|string
	 */
	public static function getFilename($nodeId, $url, $type = 'files', $time = 0)
	{
		$module = Module::getRootModuleClass();

		$filename = $module::getCacheDir($type) . DIRECTORY_SEPARATOR . $nodeId;

		if (file_exists($filename)) {

			//+ redirect on timestamps lower then the cached one

			if ($time == 0) {
				$time = self::getCachedTimestamp($url);
				if (!$time) {
					//retrieve and save in fast cache
					# get file headers
					$headers = get_headers($url, 1);
					self::setCachedTimestamp($url, $headers);
				}
			}

			# check if file exists on the source
			if ($time > self::getCachedTimestamp($url)) { //filemtime($filename)
				return static::loadRemoteFile($url, $filename);
			} else {
				return $filename;
			}
		} else {
			return static::loadRemoteFile($url, $filename);
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $sourceUrl
	 * @return int | bool
	 */
	public static function getCachedTimestamp($sourceUrl)
	{
		$persistor = \Foomo\Cache\Manager::getFastPersistor();
		return $persistor->directLoad($sourceUrl);
	}

	/**
	 * @param string $sourceUrl
	 * @param array $headers see $http_response_header
	 * @return mixed
	 */
	public static function setCachedTimestamp($sourceUrl, $headers)
	{
		if ($headers && strstr($headers[0], '200') !== false && isset($headers['Last-Modified'])) {
			$dt = new \DateTime($headers['Last-Modified']);
			$timestamp = $dt->getTimestamp();
			$persistor = \Foomo\Cache\Manager::getFastPersistor();
			$persistor->directSave($sourceUrl, $timestamp);
			return $timestamp;
		}
		return null;
	}

	public static function getTimestamp($url)
	{
		$timestamp = self::getCachedTimestamp($url);
		if (!$timestamp) {
			$headers = get_headers($url, 1);
			$timestamp = self::setCachedTimestamp($url, $headers);
		}
		return $timestamp;
	}


	/**
	 * Loads a remote file and returns it's local file name
	 *
	 * @param string $url
	 * @param string $filename
	 * @return bool|string
	 */
	private static function loadRemoteFile($url, $filename)
	{
		$content = @file_get_contents($url);
		if ($content) {
			self::setCachedTimestamp($url, $http_response_header);
			file_put_contents($filename, $content);
			return $filename;
		} else {
			return false;
		}
	}
}
