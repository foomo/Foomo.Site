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
use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\MVC;
use Foomo\Site;
use Foomo\Site\Adapter;
use Foomo\Site\Module;
use Foomo\Utils;

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
		MVC::redirect('viewList', compact('dimension'));
	}

	/**
	 * @param string $dimension
	 */
	public function actionViewList($dimension = null)
	{
		$this->model->setDimension($dimension);
	}

	/**
	 * @param string $dimension
	 */
	public function actionViewDump($dimension = null)
	{
		$this->model->setDimension($dimension);
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 */
	public function actionUpdateServer($action, $dimension = null)
	{
		$service = new Site\Service\ContentServer();
		$result = $service->update();

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
	public function actionUpdateCaches($action, $dimension = null)
	{
		MVC::abort();
		header('Content-Type: text/plain');
		$repo = \Foomo\Site\Module::getSiteContentServerProxyConfig()->getProxy()->getRepo();
		$res = [];
		foreach ($repo as $dimension => $repoNode) {
			$res = array_merge($this->updateRepoNodeCache($dimension, $repoNode), $res);
		}
		echo implode(PHP_EOL, $res);
		exit;
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 * @param string $nodeId
	 * @param bool   $all
	 */
	public function actionDeleteCaches($action, $dimension = null, $nodeId = null, $all = false)
	{
		$this->deleteContentCache($nodeId, ($all) ? null : $dimension);
		MVC::redirect($action, [$dimension]);
	}

	/**
	 * @param string $action
	 * @param string $dimension
	 */
	public function actionRestartServer($action, $dimension = null)
	{
		$config = Module::getSiteContentServerProxyConfig();
		if (ServerManager::serverIsRunning($config)) {
			ServerManager::kill($config);
			ServerManager::startServer($config);
		}
		MVC::redirect($action, [$dimension]);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $nodeId
	 * @param string $dimension
	 */
	private function deleteContentCache($nodeId = null, $dimension = null)
	{
		if (is_null($nodeId) && is_null($dimension)) {
			$expr = null;
		} else if (!is_null($nodeId) && is_null($dimension)) {
			$expr = Expr::propEq('nodeId', $nodeId);
		} else if (!is_null($nodeId) && !is_null($dimension)) {
			$expr = Expr::groupAnd(
				Expr::propEq('nodeId', $nodeId),
				Expr::propEq('dimension', $dimension)
			);
		} else if (is_null($nodeId) && !is_null($dimension)) {
			$expr = Expr::propEq('dimension', $dimension);
		}
		Adapter::invalidateCachedLoadClientContent($expr);
	}

	/**
	 * @todo use KRAKEN to warm caches
	 *
	 * @param string $dimension
	 * @param RepoNode $repoNode
	 * @return string[]
	 */
	private function updateRepoNodeCache($dimension, $repoNode)
	{
		$ret = [];
		$time = microtime(true);
		$url = Utils::getServerUrl(false, true) . $repoNode->URI;

		if (
			in_array(
				$repoNode->mimeType,
				[
					'application/neos+page',
					'application/foomo+app',
					'application/shop+category',
				]
			)
		) {
			//trigger_error('warming cache for ' . $url);

			// delete cache
			$this->deleteContentCache($repoNode->id, $dimension);

			// curl site to build cache
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			// @todo images are not being loaded/cached
			if (curl_exec($ch) === false) {
				$ret[] = 'ERROR ' . curl_error($ch) . '(' . $url .')';
				// @todo for now we are stopping here
				echo implode(PHP_EOL, $ret);
				exit;
			} else {
				$ret[] = 'Cached (' . number_format((microtime(true) - $time), 2) . 's): ' . $url;
			}
			curl_close($ch);
		}

		# iterate children
		if ($repoNode->nodes && !empty($repoNode->nodes)) {
			foreach ($repoNode->nodes as $childRepoNode) {
				$ret = array_merge($this->updateRepoNodeCache($dimension, $childRepoNode), $ret);
			}
		}
		return $ret;
	}
}
