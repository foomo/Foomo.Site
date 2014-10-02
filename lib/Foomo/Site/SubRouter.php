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

use Foomo\Site;
use Foomo\Router\Route;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class SubRouter extends \Foomo\Router
{
	// --------------------------------------------------------------------------------------------
	// ~ Static variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @todo: should this be a static var?
	 *
	 * @var string
	 */
	public static $prefix = '/subroute';

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Simple error handling route
	 */
	public function error()
	{
		header("HTTP/1.0 404 Not Found");
		echo 'file not found';
		exit;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Protected static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns the path including the prefix
	 *
	 * @param string $path
	 * @return string
	 */
	protected static function getUri($path)
	{
		return static::$prefix . $path;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Internal public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Returns an internal route with the prefix
	 *
	 * @internal
	 * @return Route
	 */
	public static function getSubRoute()
	{
		return Route::createWithPath(static::$prefix . '/*', [get_called_class(), 'executeSubRoute']);
	}

	/**
	 * Executes the sub router without the prefix
	 *
	 * @internal
	 */
	public static function executeSubRoute()
	{
		$router = new static();
		$router->execute(substr(Site::getRouter()->currentPath, strlen(static::$prefix)));
	}
}
