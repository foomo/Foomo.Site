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
use Foomo\Site\ContentServer\NodeIterator;
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
			$siteRepoNodes = (object) [];
			foreach (Site::getConfig()->adapters as $adapter) {
				$adapterConfig = $adapter::getAdapterConfig();
				if ($adapterConfig && $adapterConfig->getPathUrl('repository')) {
					$adapterRepoNodes = static::getRepoNode($adapterConfig->getPathUrl('repository'));
					$adapterRepoNodes = static::validateRepoNodes($adapterRepoNodes);
					foreach ($adapterRepoNodes as $dimension => $repoNode) {
						static::iterateNode($dimension, $repoNode);
					}
					$siteRepoNodes = static::mergeRepoNodes($siteRepoNodes, $adapterRepoNodes);
				}
			}
			return $siteRepoNodes;
		} catch (\Exception $e) {
			trigger_error('EXCEPTION ('.$e->getCode().'): ' . $e->getMessage(), E_USER_WARNING);
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
	 * @return mixed
	 */
	protected static function getRepoNode($url)
	{
		return json_decode(file_get_contents($url));
	}

	/**
	 * Iterates over the repo node
	 *
	 * @param string   $dimension
	 * @param mixed $repoNode
	 */
	protected static function iterateNode($dimension, $repoNode)
	{
		static::validateNode($dimension, $repoNode);
		# iterate child nodes
		foreach (NodeIterator::getIterator($repoNode) as $childRepoNode) {
			static::iterateNode($dimension, $childRepoNode);
		}
	}

	/**
	 * Validate and modify the node
	 *
	 * @param mixed $repoNodes
	 * @return mixed
	 */
	protected static function validateRepoNodes($repoNodes)
	{
		return $repoNodes;
	}

	/**
	 * Validate and modify the node
	 *
	 * @param string $dimension
	 * @param mixed $repoNode
	 */
	protected static function validateNode($dimension, $repoNode)
	{
		// implement me if needed
	}

	/**
	 * Simple merge handling
	 *
	 * @param mixed $siteRepoNode
	 * @param mixed $adapterRepoNode
	 * @return mixed
	 */
	protected static function mergeRepoNodes($siteRepoNode, $adapterRepoNode)
	{
		foreach ($adapterRepoNode as $adapterDimension => $repoNode) {
			$siteRepoNode->$adapterDimension = $repoNode;
		}
		return $siteRepoNode;
	}
}
