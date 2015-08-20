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

namespace Foomo\Site\ContentServer;

use Foomo\ContentServer\Vo;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Client
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns content from the configured content server
	 *
	 * @param string   $uri
	 * @param string[] $dimensions
	 * @param array    $data
	 * @return Vo\Content\SiteContent
	 */
	public static function getContent($uri, array $dimensions, array $data = [])
	{
		$env = Site::getEnv();
		$config = Site::getConfig();

		# create request env
		$contentEnv = Vo\Requests\Content\Env::create($dimensions, $env::getGroups(), $data);

		# create request
		$request = Vo\Requests\Content::create($uri, $contentEnv);

		# append request nodes
		foreach ($config->navigations as $id => $navigation) {
			$request->addNode(
				$id,
				$navigation['id'],
				$navigation['mimeTypes'],
				$navigation['expand'],
				$navigation['dataFields']
			);
		}

		# retrieve and return content
		return static::getContentServerProxy()->getContent($request);
	}

	/**
	 * @param array    $nodes
	 * @param string[] $dimensions
	 * @param array    $data
	 * @return Vo\Content\Node[]
	 */
	public static function getNodes(array $nodes, array $dimensions, array $data = [])
	{
		$env = Site::getEnv();

		$contentEnv = Vo\Requests\Content\Env::create($dimensions, $env::getGroups(), $data);
		$request = Vo\Requests\Nodes::create($contentEnv);

		# append request nodes
		foreach ($nodes as $node) {
			$request->addNode(
				$node['name'],
				$node['id'],
				$node['mimeTypes'],
				$node['expand'],
				(isset($node['dataFields'])) ? $node['dataFields'] : []
			);
		}

		return static::getContentServerProxy()->getNodes($request);
	}

	/**
	 * @param string   $dimension
	 * @param string[] $ids
	 *
	 * @return string[]
	 */
	public static function getURIs($dimension, $ids)
	{
		return static::getContentServerProxy()->getURIs($dimension, $ids);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the configured site content server proxy
	 *
	 * @return \Foomo\ContentServer\ProxyInterface
	 *
	 * @throws Site\Exception\HTTPException
	 */
	protected static function getContentServerProxy()
	{
		static $inst;
		if (is_null($inst)) {
			try {
				$inst = Site\Module::getSiteContentServerProxyConfig()->getProxy();
			} catch (\Exception $e) {
				throw new Site\Exception\HTTPException(503, Site\Exception\HTTPException::MSG_CONTENT_SERVER_UNAVAILABLE, $e);
			}
		}
		return $inst;
	}
}
