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

namespace Foomo\Site\Service;

use Foomo\Cache\Invalidator;
use Foomo\Cache\Manager;
use Foomo\Cache\Persistence\Expr;

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

	const VERSION = '1.0';

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @note If you change the api here, make sure to check the Neos publishing call!
	 *
	 * @param string $nodeId
	 * @param string $dimension
	 * @param string $clientClass
	 * @return bool
	 */
	public function deleteClientCache($nodeId = null, $dimension = null, $clientClass = null)
	{
		trigger_error(__FUNCTION__);
		$exprs = [];

		if (is_null($nodeId)) {
			$exprs[] = Expr::statusValid();
		} else {
			$exprs[] = Expr::propEq('nodeId', $nodeId);
		}

		if (!is_null($dimension)) {
			$exprs[] = Expr::propEq('dimension', $dimension);
		}

		if (!is_null($clientClass)) {
			$exprs[] = Expr::propEq('clientClass', $clientClass);
		}

		if (count($exprs) > 1) {
			$expr = call_user_func(['Foomo\Cache\Persistence\Expr', 'groupAnd'], $exprs);
		} else {
			$expr = $exprs[0];
		}

		try {
			Manager::invalidateWithQuery(
				'Foomo\\Site\\Adapter::cachedLoadClientContent', $expr, true, Invalidator::POLICY_DELETE
			);
			return true;
		} catch (\Exception $e) {
			trigger_error("Cache invalidation failed for $nodeId with message " . $e->getMessage(), E_USER_WARNING);
			return false;
		}
	}
}
