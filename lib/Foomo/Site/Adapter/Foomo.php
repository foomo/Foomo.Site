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

namespace Foomo\Site\Adapter;

use Foomo\ContentServer\Vo;
use Foomo\MVC;
use Foomo\Router\MVC\URLHandler;
use Foomo\Site;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Foomo extends AbstractBase
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function getName()
	{
		return 'foomo';
	}

	/**
	 * @inheritdoc
	 */
	public static function getSubRoutes()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public static function getModuleResources()
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public static function getAdapterConfig()
	{
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public static function getContent($siteContent)
	{
		$className = false;

		if (isset($siteContent->data->appClassName)) {
			$className = $siteContent->data->appClassName;
		}
		if (isset($siteContent->data->appData)) {
			$data = $siteContent->data->appData;
		}

		if ($className) {
			$classes = [
				$className,
				$className . '\\Frontend',
			];
			foreach ($classes as $class) {
				if (class_exists($class)) {
					return call_user_func_array([$class, 'run'], [$data, $siteContent->URI]);
				}
			}
			return '<pre>No App found for: ' . implode(', ', $classes) . '</pre>';
		} else {
			return '<pre>No App Class Name defined</pre>';
		}
	}
}
