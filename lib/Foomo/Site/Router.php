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
use Foomo\MVC\URLHandler;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
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

		$this->addRoutes([
			'/robots.txt'	=> 'robots',
			'/*'          => 'site',
		]);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function robots()
	{
		\Foomo\Session::disable();
		header('Content-Type: text/plain');
		echo 'User-agent: *' . PHP_EOL;
		if (Config::isProductionMode()) {
			echo 'Sitemap: /sitemap.xml' . PHP_EOL;
		} else {
			echo 'Disallow: /' . PHP_EOL;
		}
		exit;
	}

	/**
	 * @param string $region
	 * @param string $language
	 * @return mixed
	 */
	public function site($region=null, $language=null)
	{
		$config = Module::getSiteConfig();

		$url = parse_url($_SERVER['REQUEST_URI']);
		var_dump($url);
		exit;

		// @todo: here happened some crazy stuff
		$basePath = $url->path;

		return \Foomo\MVC::run(\Foomo\Site::getFrontend(), $basePath);
	}
}