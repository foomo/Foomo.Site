<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Site\Adapter;

use Foomo\Cache;
use Foomo\Site\Adapter\Neos;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
abstract class AbstractClient
{
	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Load content from the remote content server
	 *
	 * @param string   $nodeId
	 * @param string[] $env
	 * @return string
	 */
	protected static function load($nodeId, $env = [])
	{
		return Cache\Proxy::call(get_called_class(), 'cachedLoad', [$nodeId, $env]);
	}
}
