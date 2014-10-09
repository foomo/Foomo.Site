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

namespace Foomo\Site\Adapter\Neos;

use Foomo\Media\Image;
use Foomo\Site\Adapter\Media;
use Foomo\Site\Adapter\Neos;
use Foomo\Site\Cache;
use Foomo\Utils;

/**
 * @todo    check image & asset route
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class SubRouter extends \Foomo\Site\SubRouter
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	public static $prefix = '/neos';

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addRoutes(
			[
				'/asset/:nodeId/:filename'   => 'asset',
				'/image/:type/:nodeId/:time' => 'image',
				'/image/:type/:nodeId'       => 'image',
				'/*'                         => 'error',
			]
		);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public route methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the route for handling assets
	 *
	 * @param string $nodeId
	 * @param string $filename
	 * @return string
	 */
	public static function getAssetUri($nodeId, $filename)
	{
		return static::getUri('/asset/' . $nodeId . '/' . $filename);
	}

	/**
	 * Returns the route for handling images
	 *
	 * @param string $type
	 * @param string $nodeId
	 * @return string
	 */
	public static function getImageUri($type, $nodeId)
	{
		return static::getUri('/image/' . $type . '/' . $nodeId);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Server assets i.e. pdf, zip
	 *
	 * @param string $nodeId
	 * @param string $filename
	 */
	public function asset($nodeId, $filename)
	{
		$config = Neos::getAdapterConfig();
		$url = $config->getPathUrl('asset') . '/' . $nodeId;
		$cacheFilename = Cache::getFilename($nodeId, $url);

		if ($cacheFilename) {
			Utils::streamFile($cacheFilename, $filename, 'application/octet-stream', true);
		} else {
			$this->error();
		}
	}

	/**
	 * @todo: is there a way not to hardcode 'neos'?
	 * Serve responsive images
	 *
	 * @param string $type
	 * @param string $nodeId
	 * @param string $time
	 */
	public function image($type, $nodeId, $time = null)
	{
		$config = Neos::getAdapterConfig();
		$url = $config->getPathUrl('image') . '/' . $nodeId;
		$cacheFilename = Cache::getFilename($nodeId, $url, 'neos', (int) $time);

		if ($cacheFilename) {
			Image\Server::serve($cacheFilename, 'neos', $type);
		} else {
			$this->error();
		}
	}
}
