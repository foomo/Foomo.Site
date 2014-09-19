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

use Foomo\Site;
use Foomo\ContentServer\Vo;

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
	 * @param string $uri
	 * @return Vo\Content\SiteContent
	 */
	public static function getContent($uri)
	{
		$config = Site::getConfig();
		$session = Site::getSession();

		# create request env
		$env = Vo\Requests\Content\Env::create(
		# default env
			Vo\Requests\Content\Env\Defaults::create(
				$session::getRegion(),
				$session::getLanguage()
			),
			$session::getGroups()
		);
		$env->state = $session::getState();

		# create request
		$request = Vo\Requests\Content::create($uri, $env);

		# append request nodes
		foreach ($config->navigations as $id => $navigation) {
			$request->addNode($id, $navigation['id'], $navigation['mimeTypes'], $navigation['expand']);
		}

		# retrieve and return content
		return static::getContentServerProxy()->getContent($request);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the configured site content server proxy
	 *
	 * @return \Foomo\ContentServer\ProxyInterface
	 */
	protected static function getContentServerProxy()
	{
		static $inst;
		if (is_null($inst)) {
			$inst = Site\Module::getSiteContentServerProxyConfig()->getProxy();
		}
		return $inst;
	}
}
