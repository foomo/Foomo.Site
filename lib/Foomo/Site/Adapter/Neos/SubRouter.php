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
				'/image/:type/:nodeId/:time/:timestamp' => 'imageWithTimestamp',

				'/image/:type/:nodeId/:time' => 'imageWithTimestamp',
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
	public static function getImageUri($type, $nodeId, $timestamp = null)
	{
		if (empty($timestamp)) {
			return static::getUri('/image/' . $type . '/' . $nodeId);
		}
		return static::getUri('/image/' . $type . '/' . $nodeId .'/time/' . $timestamp);
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
		$config = static::getAdapterConfig();
		$url = $config->getPathUrl('asset') . '/' . $nodeId;
		\Foomo\Timer::start($topic = 'Foomo\Site\Adapter\Cache::getFilename');
		$cacheFilename = Cache::getFilename($nodeId, $url);
		\Foomo\Timer::stop($topic);

		if ($cacheFilename) {
			\Foomo\Timer::start($topic = 'Foomo\Utils::streamFile');
			Utils::streamFile($cacheFilename, $filename, Utils::guessMime($cacheFilename), true);
			\Foomo\Timer::stop($topic);
			\Foomo\Timer::addMarker('done');
			#\Foomo\Timer::writeStatsToFile();
			exit;
		} else {
			$this->error();
		}
	}

	public function imageWithTimestamp($type, $nodeId, $time = null, $timestamp = 0) {
		//restructure uri segments for backward compatibility reasons
		if (is_numeric($time) && $timestamp == 0) {
			$timestamp = $time;
			$time = 'time';
		}

		if ($time == 'time' && is_numeric($timestamp)) {
			$sourceFile = Cache::getSourceFilename($nodeId, static::$prefix);
			if (file_exists($sourceFile)) {

				$cachedTimestamp = filemtime($sourceFile);

				//redirect
				if ($cachedTimestamp != $timestamp) {
					$this->redirect(self::getImageUri($type, $nodeId, $cachedTimestamp));
				}
			}
		} else {
			//error
			$this->error();
		}
		$this->image($type, $nodeId, $timestamp);

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

		$config = static::getAdapterConfig();
		$url = $config->getPathUrl('image') . '/' . $nodeId;

		\Foomo\Timer::start($topic = 'Foomo\Site\Adapter\Cache::getFilename');


		$cacheFilename = Cache::getFilename($nodeId, $url, static::$prefix, (int) $time);
		\Foomo\Timer::stop($topic);


		if ($cacheFilename) {
			\Foomo\Timer::start($topic = 'Foomo\Media\Image\Server::serve');

			//prevent upscaling the original image
			$allowResizeAboveSourceSetting = Image\Processor::getAllowResizeAboveSource();

			Image\Processor::allowResizeAboveSource(false);

			Image\Server::serve($cacheFilename, Neos::getName(), $type, null, (((int)microtime()) + 30 * 24 * 3600));

			//set back to what it was - as we disabled it for neos
			Image\Processor::allowResizeAboveSource($allowResizeAboveSourceSetting);

			\Foomo\Timer::stop($topic);

			\Foomo\Timer::addMarker('done');
			#\Foomo\Timer::writeStatsToFile();
			exit;
		} else {
			$this->error();
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return \Foomo\Config\AbstractConfig|\Foomo\Site\Adapter\DomainConfig
	 */
	public static function getAdapterConfig()
	{
		return Neos::getAdapterConfig();
	}
}
