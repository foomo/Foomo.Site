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

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
interface AdapterInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Return the name of the adapter i.e. `neos`
	 *
	 * @return string
	 */
	static function getName();

	/**
	 * Returns list of resources required for the root module
	 *
	 * @return \Foomo\Modules\Resource[]
	 */
	static function getModuleResources();

	/**
	 * Returns list of sub routes
	 *
	 * @return \Foomo\Site\Adapter\Neos\SubRouter[]
	 */
	static function getSubRoutes();

	/**
	 * Return class that retrieves content
	 *
	 * @return \Foomo\Site\Adapter\ClientInterface
	 */
	static function getClient();
}
