<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo;

use Foomo\Router\MVC\URLHandler;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Site
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Initialize session & run router
	 *
	 * @return mixed
	 */
	public static function run()
	{
		$config = Site\Module::getSiteConfig();

		/* @var $routerClass \Foomo\Router */
		$routerClass = $config->getClass("router");
		/* @var $sessionClass \Foomo\Site\Session */
		$sessionClass = $config->getClass("session");

		# boot session
		call_user_func($sessionClass . "::boot");

		# setup url handler
		// @todo: clarify this for me please
		URLHandler::exposeClassId(false);
		URLHandler::strictParameterHandling(false);

		# setup mvc | hide the script - you might need mod rewrite
		// @todo: clarify this for me please
		\Foomo\MVC::hideScript(true);

		# create and execute router
		$router = new $routerClass();
		return $router->execute();
	}

	/**
	 * Returns the configured site frontend
	 *
	 * @return \Foomo\Site\Frontend
	 */
	public static function getFrontend()
	{
		static $inst;
		if (is_null($inst)) {
			$config = Site\Module::getSiteConfig();
			$class = $config->getClass("frontend");
			$inst = new $class();
		}
		return $inst;
	}

	/**
	 * Returns the configured site session class name
	 *
	 * @return string|\Foomo\Site\SessionInterface
	 */
	public static function getSession()
	{
		return Site\Module::getSiteConfig()->getClass("session");
	}

	/**
	 * Returns the configured site content server proxy
	 *
	 * @return \Foomo\ContentServer\ProxyInterface
	 */
	public static function getContentServerProxy()
	{
		static $inst;
		if (is_null($inst)) {
			$inst = Site\Module::getSiteContentServerProxyConfig()->getProxy();
		}
		return $inst;
	}
}
