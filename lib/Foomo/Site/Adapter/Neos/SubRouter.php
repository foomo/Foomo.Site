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
use Foomo\Site\Adapter\Cache;
use Foomo\Site\Adapter\Media;
use Foomo\Site\Adapter\Neos;
use Foomo\Utils;

/**
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
	// ~ Public route methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Server assets i.e. pdf, zip
	 *
	 * @param string $nodeId
	 * @param string $filename
	 */
	public function asset($nodeId, $filename)
	{
		\Foomo\Session::saveAndRelease();

		\Foomo\Timer::addMarker('serving asset');
		$config = Neos::getAdapterConfig();
		$url = $config->getPathUrl('asset') . '/' . $nodeId;
		\Foomo\Timer::start($topic = 'Foomo\Site\Adapter\Cache::getFilename');
		$cacheFilename = Cache::getFilename($nodeId, $url);
		\Foomo\Timer::stop($topic);

		if ($cacheFilename) {
			\Foomo\Timer::start($topic = 'Foomo\Utils::streamFile');
			Utils::streamFile($cacheFilename, $filename, Utils::guessMime($cacheFilename));
			\Foomo\Timer::stop($topic);
			\Foomo\Timer::addMarker('done');
			#\Foomo\Timer::writeStatsToFile();
			exit;
		} else {
			$this->error();
		}
	}

	/**
	 * Serve responsive images
	 *
	 * @param string $type
	 * @param string $nodeId
	 * @param string $time
	 */
	public function image($type, $nodeId, $time = null)
	{
		\Foomo\Timer::addMarker('serving neos image');
		\Foomo\Session::saveAndRelease();

		$config = Neos::getAdapterConfig();
		$url = $config->getPathUrl('image') . '/' . $nodeId;

		\Foomo\Timer::start($topic = 'Foomo\Site\Adapter\Cache::getFilename');
		$cacheFilename = Cache::getFilename($nodeId, $url, 'neos', (int) $time);
		\Foomo\Timer::stop($topic);


		if ($cacheFilename) {
			\Foomo\Timer::start($topic = 'Foomo\Media\Image\Server::serve');
			Image\Server::serve($cacheFilename, Neos::getName(), $type);
			\Foomo\Timer::stop($topic);

			\Foomo\Timer::addMarker('done');
			#\Foomo\Timer::writeStatsToFile();
			exit;
		} else {
			$this->error();
		}
	}
}
