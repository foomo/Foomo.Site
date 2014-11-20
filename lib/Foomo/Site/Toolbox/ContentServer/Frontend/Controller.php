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

namespace Foomo\Site\Toolbox\ContentServer\Frontend;

use Foomo\Cache\Persistence\Expr;
use Foomo\ContentServer\ServerManager;
use Foomo\MVC;
use Foomo\Site\Adapter;
use Foomo\Site\Module;

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
	 * @param string $dimension
	 */
	public function actionDefault($dimension = null)
	{
		MVC::redirect('list', compact('dimension'));
	}

	/**
	 * @param string $dimension
	 */
	public function actionList($dimension = null)
	{
		$this->model->setDimension($dimension);
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 * @param string $nodeId
	 * @param bool   $all
	 */
	public function deleteCachedContent($action, $dimension, $nodeId, $all = false)
	{
		if (is_null($nodeId) && is_null($dimension)) {
			$expr = null;
		} else if (!$all) {
			$expr = Expr::propEq('nodeId', $nodeId);
		} else {
			$expr = Expr::groupAnd(
				Expr::propEq('nodeId', $nodeId),
				Expr::propEq('dimension', $dimension)
			);
		}
		Adapter::invalidateCachedLoadClientContent($expr);
		MVC::redirect($action, [$dimension]);
	}

	/**
	 * @param string $dimension
	 */
	public function actionDump($dimension = null)
	{
		$this->model->setDimension($dimension);
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 */
	public function actionUpdate($action, $dimension = null)
	{
		$result = Module::getSiteContentServerProxyConfig()->getProxy()->update();

		if ($result->success) {
			MVC::redirect($action, [$dimension]);
		} else {
			MVC::abort();
			var_dump($result);
			exit;
		}
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 */
	public function actionRestart($action, $dimension = null)
	{
		$config = Module::getSiteContentServerProxyConfig();
		if (ServerManager::serverIsRunning($config)) {
			ServerManager::kill($config);
			ServerManager::startServer($config);
		}
		MVC::redirect($action, [$dimension]);
	}
}
