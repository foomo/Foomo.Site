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

use Foomo\ContentServer\Vo;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Neos extends AbstractBase
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function getName()
	{
		return 'neos';
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubRoutes()
	{
		$routes = [];

		foreach (static::getAdapterConfig()->subRouters as $subRouter) {
			$routes[$subRouter::$prefix] = $subRouter::getSubRoute();
		}
		return $routes;
	}

	/**
	 * @inheritdoc
	 * @throws Site\Exception\HTTPException
	 */
	public static function getContent($siteContent)
	{
		if($siteContent->mimeType == 'application/neos+external') {
			if(!isset($siteContent->data) && !isset($siteContent->data->url)) {
				throw new Site\Exception\HTTPException(500, 'Could not resolve external link');
			}
			\Foomo\MVC::abort();
			$location = htmlentities($siteContent->data->url);
			header("Location: $location", true, 301);
			exit;
		}

		/* @var $client ClientInterface */
		$client = static::getAdapterConfig()->getClass('client');
		return $client::get($siteContent->dimension, $siteContent->item->id, $siteContent->URI);
	}
}
