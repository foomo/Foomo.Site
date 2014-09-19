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

use Foomo\Media\Image\Adaptive;
use Foomo\Media\Image\Adaptive\RuleSet;
use Foomo\Media\Image\Server;
use Foomo\Site\Module;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Media
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Serves media from the remote content server by loading it to the
	 * local file system and serving it through the foomo media image server
	 *
	 * @param DomainConfig $config the adapter config
	 * @param string       $nodeId
	 * @param string       $layout
	 * @param string       $type
	 * @param RuleSet      $ruleSet
	 * @return bool
	 */
	public static function serve($config, $nodeId, $layout, $type, $ruleSet = null)
	{
		$file = static::getLocalFile($nodeId);

		if (!file_exists($file)) {
			$url = $config->getPathUrl('media') . '/' . $nodeId;
			$file = static::loadRemoteFile($url, $file);
		}

		if ($file) {
			Server::serve($file, $layout, $type, $ruleSet);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns the local file name
	 *
	 * @param string $nodeId
	 * @return string
	 */
	public static function getLocalFile($nodeId)
	{
		$module = Module::getRootModuleClass();
		return $module::getVarDir('media/neos') . DIRECTORY_SEPARATOR . $nodeId;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Loads a remote file and returns it's local file name
	 *
	 * @param string $url
	 * @param string $filename
	 * @return bool|string
	 */
	private static function loadRemoteFile($url, $filename)
	{
		$content = file_get_contents($url);
		if ($content) {
			file_put_contents($filename, $content);
			return $filename;
		} else {
			return false;
		}
	}
}
