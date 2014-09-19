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

use Foomo\Site\Adapter\Media;
use Foomo\Site\Adapter\Neos;

/**
 * @link www.foomo.org
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

		$this->addRoutes([
//			'/asset/:assetId/:filename' => 'asset',
			'/image/:type/:nodeId' => 'image',
			'/*' => 'error',
		]);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

//	/**
//	 * output asset content
//	 *
//	 * @param string $assetId
//	 * @param string $filename
//	 */
//	public function asset($assetId, $filename)
//	{
//		/* @var $config Config */
//		$config = Module::getConfig(Config::NAME);
//		$asset = file_get_contents($config->getPathtUrl('media') . '/' . $assetId);
//
//		if ($asset) {
//			header('Content-Description: File Transfer');
//			header('Content-Type: application/octet-stream');
//			header('Content-Disposition: attachment; filename='.$filename);
//			header('Content-Transfer-Encoding: binary');
//			header('Expires: 0');
//			header('Cache-Control: must-revalidate');
//			header('Pragma: public');
//			header('Content-Length: ' . strlen($asset));
//			ob_clean();
//			flush();
//			echo $asset;
//			exit;
//		} else {
//			$this->error();
//		}
//	}

	/**
	 * @param string $type
	 * @param string $nodeId
	 */
	public function image($type, $nodeId)
	{
		$config = Neos::getAdapterConfig();
		// @todo: is there a way not to hardcode 'full'?
		if (!Media::serve($config, $nodeId, 'full', $type)) {
			$this->error();
		};
	}

	/**
	 * @param string $type
	 * @param string $nodeId
	 * @return string
	 */
	public static function getImageUri($type, $nodeId)
	{
		return static::getUri('/image/' . $type . '/' . $nodeId);
	}
}
