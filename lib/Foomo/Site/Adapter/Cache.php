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
		$filename = self::getSourceFilename($nodeId, $type);

		if (file_exists($filename)) {

			if ($time == 0) {
				$time = filemtime($filename);
			}

			# check if file exists on the source
			if ($time > filemtime($filename)) {
				return static::loadRemoteFile($url, $filename);
			} else {
				return $filename;
			}
		} else {
			return static::loadRemoteFile($url, $filename);
		}
	}


	public static function getSourceFilename($nodeId, $type = 'files') {
		$module = Module::getRootModuleClass();
		return $module::getCacheDir($type) . DIRECTORY_SEPARATOR . $nodeId;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------


	/**
	 * @param array $headers see $http_response_header
	 * @return int
	 */
	public static function extractLastModifiedFromHeader($headers)
	{
		if ($headers && strstr($headers[0], '200') !== false && isset($headers['Last-Modified'])) {
			$dt = new \DateTime($headers['Last-Modified']);
			$timestamp = $dt->getTimestamp();
			if ($timestamp > time()) {
				$timestamp = time();
			}
			return $timestamp;
		}
		return time();
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
			file_put_contents($filename, $content);
			//manipulate the file mtime
			touch($filename, self::extractLastModifiedFromHeader($http_response_header));
			return $filename;
		} else {
			return false;
		}
	}
}
