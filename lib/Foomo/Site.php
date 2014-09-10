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
use Foomo\Site\Module;

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
	 * @return mixed
	 */
	public static function run()
	{
		$config = Module::getSiteConfig();

		/* @var $routerClass \Foomo\Router */
		$routerClass = $config->getClass("router");
		/* @var $sessionClass \Foomo\Site\Session */
		$sessionClass = $config->getClass("session");

		# boot session
		call_user_func($sessionClass . "::boot");

		# setup url handler
		URLHandler::exposeClassId(false);
		URLHandler::strictParameterHandling(false);

		# setup mvc
		# hide the script - you might need mod rewrite
		\Foomo\MVC::hideScript(true);

		# create and execute router
		$router = new $routerClass();
		return $router->execute();
	}

	/**
	 * @return \Foomo\Site\Frontend
	 */
	public static function getFrontend()
	{
		static $inst;
		if (is_null($inst)) {
			$config = Module::getSiteConfig();
			$class = $config->getClass("frontend");
			$inst = new $class();
		}
		return $inst;
	}
}