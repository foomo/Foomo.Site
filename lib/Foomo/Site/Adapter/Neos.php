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

use Foomo\Site;
use Foomo\ContentServer\Vo;

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
	 *
	 * @return string
	 */
	public static function getName()
	{
		return 'neos';
	}

	/**
	 * @inheritdoc
	 *
	 * @return \Foomo\Site\Adapter\Neos\SubRouter[]
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
	 *
	 * @param string   $nodeId
	 * @param string   $region
	 * @param string   $language
	 * @param string[] $groups
	 * @param string   $state
	 * @param string   $baseURL
	 * @return string
	 */
	public static function getContent($nodeId, $region, $language, $groups, $state, $baseURL)
	{
		return Site\Adapter\Neos\Content::get($nodeId, $region, $language, $groups, $state, $baseURL);
	}
}