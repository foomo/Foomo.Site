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

use Foomo\Cache\Persistence\Expr;
use Foomo\MVC;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Controller
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var Model
	 */
	public $model;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function actionDefault()
	{
		MVC::redirect('cachedContent');
	}

	/**
	 *
	 */
	public function actionCachedContent()
	{
	}

	/**
	 * @param string $nodeId
	 * @param string $dimension
	 */
	public function actionDeleteCachedContent($nodeId=null, $dimension = null)
	{
		if (is_null($nodeId) && is_null($dimension)) {
			$expr = null;
		} else if (is_null($dimension)) {
			$expr = Expr::propEq('nodeId', $nodeId);
		} else {
			$expr = Expr::groupAnd(
				Expr::propEq('nodeId', $nodeId),
				Expr::propEq('dimension', $dimension)
			);
		}

		$this->model->deleteCachedContent($expr);
		MVC::redirect('cachedContent');
	}
}
