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

namespace Foomo\Site;

use Foomo\Config;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Router extends \Foomo\Router
{
	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$config = Module::getSiteConfig();

		# add configured sub routes
		foreach ($config->subRouters as $subRouter) {
			$this->addRoutes(
				[
					$subRouter::$prefix => $subRouter::getSubRoute()
				]
			);
		}

		# add adapter sub routes
		foreach ($config->adapters as $adapter) {
			$this->addRoutes($adapter::getSubRoutes());
		}

		# add default routes
		$this->addRoutes(
			[
				'/robots.txt' => 'robots',
				'/*'          => 'index',
			]
		);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public route methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Default route
	 *
	 * @return string
	 */
	public function index()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		return \Foomo\MVC::run(\Foomo\Site::getFrontend(), $url["path"]);
	}

	/**
	 * Simple robots.txt route
	 */
	public function robots()
	{
		\Foomo\Session::disable();
		header('Content-Type: text/plain');
		echo 'User-agent: *' . PHP_EOL;
		if (!Config::isProductionMode()) {
			echo 'Disallow: /' . PHP_EOL;
		}
		exit;
	}
}
