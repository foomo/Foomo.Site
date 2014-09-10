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
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Bundles
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $debug
	 * @return \Foomo\Bundle\AbstractBundle
	 * @throws \Exception
	 */
	public static function lteie8Scripts($debug=false)
	{
		return \Foomo\JS\Bundle::create("foomo-site-lteie8")
			->addJavaScripts([
				Module::getBaseDir('js') . DIRECTORY_SEPARATOR . 'lteie8.js'
			])->debug($debug)
		;
	}

	/**
	 * @param bool $debug
	 * @return \Foomo\Bundle\AbstractBundle
	 * @throws \Exception
	 */
	public static function siteScripts($debug=false)
	{
		return \Foomo\JS\Bundle::create("foomo-site-scripts")
			->addJavaScripts([
				Module::getBaseDir('js') . DIRECTORY_SEPARATOR . 'site.js'
			])->debug($debug)
		;
	}

	/**
	 * @param bool $debug
	 * @return \Foomo\Bundle\AbstractBundle
	 * @throws \Exception
	 */
	public static function siteStyles($debug=false)
	{
		return \Foomo\Sass\Bundle::create(
			"foomo-site-styles",
			Module::getBaseDir('sass') . DIRECTORY_SEPARATOR  . 'site.scss'
		)->debug($debug);
	}
}