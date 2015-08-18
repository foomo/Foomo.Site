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

namespace Foomo\Site\SubRouter;

use Foomo\Site;
use Foomo\Site\Exception\HTTPException;
use Foomo\Site\SubRouter;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class ContentServer extends SubRouter
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static $prefix = '/contentserver';

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
				'/export/:format' => 'export',
				'/export'         => 'export',
				'/*'              => 'error',
			]
		);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $format one of json|text
	 * @throws HTTPException
	 */
	public function export($format = 'json')
	{
		/* @var $contentServer Site\ContentServerInterface */
		$contentServer = Site::getConfig()->getClass('contentServer');
		$data = $contentServer::export();

		if (is_null($format)) {
			$format = 'json';
		}

		if ($data === false) {
			throw new HTTPException(503);
		} else {
			switch ($format) {
				case 'json':
					header('Content-Type: application/json');
					if (defined('JSON_PRETTY_PRINT')) {
						$result = json_encode($data, JSON_PRETTY_PRINT);
					} else {
						$result = json_encode($data);
					}
					if(is_null($result)) {
						trigger_error("unable to encode json. json error code: " . json_last_error(), E_USER_WARNING);
						throw new HTTPException(500);
					}
					echo $result;
					break;
				case 'serialzed':
					header('Content-Type: text/plain;charset=utf-8;');
					ini_set("html_errors", "Off");
					echo serialize($data);
					break;
				case 'text':
					header('Content-Type: text/plain;charset=utf-8;');
					ini_set("html_errors", "Off");
					var_dump($data);
					break;
				default:
					trigger_error("bad request", E_USER_ERROR);
					break;
			}
		}
	}
}
