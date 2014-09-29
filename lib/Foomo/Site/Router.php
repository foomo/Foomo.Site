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
use Foomo\Site;

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
	 * Add configured, adapter and own routes
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
				'/*'          => 'site',
			]
		);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public route methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Site route
	 *
	 * @return string
	 */
	public function site()
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		#$baseUrl = $this->parseLocale($url["path"]);

		$locale = \Foomo\Site\Utils\Uri::getLocale($url["path"]);

		var_dump($locale);

		#var_dump(\Foomo\Site\Utils\URI::getLocale($url["path"]));exit;

		// @todo: is there a better way?
		//$_SERVER['REQUEST_URI'] = $locale['uri'];
		//var_dump($_SERVER['REQUEST_URI']);

		return \Foomo\MVC::run(\Foomo\Site::getFrontend(), $locale['path']);
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

	// --------------------------------------------------------------------------------------------
	// ~ Protected methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Checks for locale pattern like:
	 *
	 * /ch-fr/
	 * /ch-fr
	 * /ch/
	 * /ch
	 * /
	 *
	 * @param string $path
	 * @return string
	 */
	protected function parseLocale($path)
	{
		$baseUrl = '';
		$region = false;
		$language = false;

		$config = Site::getConfig();
		$session = Site::getSession();

		# parse locale
		if (preg_match('/^\/(?P<region>[a-z]{2})-(?P<language>[a-z]{2})(\/|$)/', $path, $matches)) {
			$region = $matches['region'];
			$language = $matches['language'];
			$baseUrl = substr($path, 0, 6);
		} else if (preg_match('/^\/(?P<region>[a-z]{2})(\/|$)/', $path, $matches)) {
			$region = $matches['region'];
			$baseUrl = substr($path, 0, 3);
		}

		# validate locale and update session
		if ($region && $config->isValidRegion($region)) {
			if ($language && $config->isValidLanguage($region, $language)) {
				$session::setLocale($region, $language);
				return $baseUrl;
			} else {
				$session::setRegion($region);
				return $baseUrl;
			}
		}

		# invalid data so return the whole path
		return $path;
	}
}
