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

namespace Foomo\Site\Toolbox\Adapter\Frontend;

use Foomo\Cache\Invalidator;
use Foomo\Cache\Manager;
use Foomo\Cache\Persistence\Expr;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Model
{
	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @return array
	 */
	public function getCachedContent()
	{
		$resources = [];
		foreach (Site\Adapter::getCachedLoadClientContent() as $resource) {
			if (!isset($resources[$resource->properties['clientClass']])) {
				$resources[$resource->properties['clientClass']] = [];
			}
			$resources[$resource->properties['clientClass']][] = $resource;
		}

		return $resources;
	}

	/**
	 * @param Expr $expr
	 */
	public function deleteCachedContent($expr = null)
	{
		Site\Adapter::invalidateCachedLoadClientContent($expr);
	}
}
