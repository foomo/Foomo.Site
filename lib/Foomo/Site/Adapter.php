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

namespace Foomo\Site;

use Foomo\Cache;
use Foomo\CSV\Jobs\Manager;
use Foomo\Site\Adapter\Neos;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Adapter
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const CACHED_CONTENT_RESOURCE = 'Foomo\Site\Adapter::cachedLoadClientContent';

	// --------------------------------------------------------------------------------------------
	// ~ Internal static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @internal
	 * @Foomo\Cache\CacheResourceDescription
	 *
	 * @param string $clientClass
	 * @param string $dimension
	 * @param string $nodeId
	 * @param string $domain
	 * @return string
	 */
	public static function cachedLoadClientContent($clientClass, $dimension, $nodeId, $domain)
	{
		return $clientClass::load($dimension, $nodeId, $domain);
	}

	/**
	 * @param Cache\Persistence\Expr $expr
	 * @param int $limit
	 * @param int $offset
	 * @return Cache\CacheResourceIterator|Cache\CacheResource[]
	 */
	public static function getCachedLoadClientContent($expr=null, $limit=0, $offset=0)
	{
		return Cache\Manager::query(self::CACHED_CONTENT_RESOURCE, $expr, $limit, $offset);
	}

	/**
	 * @param Cache\Persistence\Expr $expr
	 * @param string $policy
	 */
	public static function invalidateCachedLoadClientContent($expr = null, $policy = Cache\Invalidator::POLICY_DELETE)
	{
		ini_set('max_execution_time', 300);
		ini_set('memory_limit','2G');
		Cache\Manager::invalidateWithQuery(self::CACHED_CONTENT_RESOURCE, $expr, true, $policy);
		//sometimes apc does not remove items. to be on the safe side clear all
		Cache\Manager::getFastPersistor()->reset();
	}
}
