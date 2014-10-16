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

namespace Foomo\Site;

use Foomo\ContentServer\Vo\Content\RepoNode;
use Foomo\SimpleData\VoMapper;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class ContentServer implements ContentServerInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function export()
	{
		try {
			$siteRepoNodes = [];
			foreach (Site::getConfig()->adapters as $adapter) {
				$adapterRepoNodes = static::getRepoNode($adapter::getAdapterConfig()->getPathUrl('repository'));
				foreach ($adapterRepoNodes as $dimension => $repoNode) {
					static::iterateNode($dimension, $repoNode);
				}
				$siteRepoNodes = static::mergeRepoNodes($siteRepoNodes, $adapterRepoNodes);
			}
			return $siteRepoNodes;
		} catch (\Exception $e) {
			return false;
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Return the repo node from a remote server
	 *
	 * @param string $url
	 * @return RepoNode[]
	 */
	protected static function getRepoNode($url)
	{
		$rawNodes = json_decode(file_get_contents($url));
		$nodes = [];
		foreach ($rawNodes as $dimension => $rawNode) {
			$nodes[$dimension] = VoMapper::map($rawNode, new RepoNode);
		}
		/* @var $repoNode RepoNode */
		return $nodes;
	}

	/**
	 * Iterates over the repo node
	 *
	 * @param string   $dimension
	 * @param RepoNode $repoNode
	 */
	protected static function iterateNode($dimension, RepoNode $repoNode)
	{
		static::validateNode($dimension, $repoNode);
		# iterate child nodes
		foreach ($repoNode as $childRepoNode) {
			static::iterateNode($dimension, $childRepoNode);
		}
	}

	/**
	 * Validate and modify the node
	 *
	 * @param $dimension
	 * @param $repoNode
	 */
	protected static function validateNode($dimension, $repoNode)
	{
		// implement me if needed
	}

	/**
	 * @param array $siteRepoNodes
	 * @param array $adapterRepoNodes
	 * @return array
	 */
	protected static function mergeRepoNodes($siteRepoNodes, $adapterRepoNodes)
	{
		return array_merge($siteRepoNodes, $adapterRepoNodes);
	}
}
