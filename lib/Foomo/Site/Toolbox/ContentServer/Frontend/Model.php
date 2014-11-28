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

use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Model
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $dimension;
	/**
	 * @var RepoNode
	 */
	public $repoNode;
	/**
	 * @var RepoNode[]
	 */
	public $repoNodes;

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param string $dimension
	 * @return $this
	 * @throws \Exception
	 */
	public function setDimension($dimension = null)
	{
		$this->repoNodes = \Foomo\Site\Module::getSiteContentServerProxyConfig()->getProxy()->getRepo();
		if ($this->repoNodes) {
			if (is_null($dimension) || !isset($this->repoNodes->$dimension)) {
				$this->dimension = array_keys(get_object_vars($this->repoNodes))[0];
			} else {
				$this->dimension = $dimension;
			}
			$this->repoNode = $this->repoNodes->{$this->dimension};
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCachedContent()
	{
		static $resources;
		if (is_null($resources)) {
			/* @var $resource \Foomo\Cache\CacheResource */
			foreach (Site\Adapter::getCachedLoadClientContent() as $resource) {
				$nodeId = $resource->properties['nodeId'];
				$dimension = $resource->properties['dimension'];
				if (!isset($resources[$nodeId])) {
					$resources[$nodeId] = [];
				}
				$resources[$nodeId][$dimension] = $resource;
			}
		}
		return $resources;
	}
}
