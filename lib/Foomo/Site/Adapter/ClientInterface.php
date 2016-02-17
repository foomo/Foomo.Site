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

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
interface ClientInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Return the content from the remote server
	 *
	 * @param string $dimension
	 * @param string $nodeId
	 * @param string $baseURL
	 * @param string $domain configuration domain name
	 * @return string
	 */
	static function get($dimension, $nodeId, $baseURL, $domain);

	/**
	 * Load the content from remote server
	 *
	 * @internal
	 *
	 * @param string $dimension
	 * @param string $nodeId
	 * @param string $domain configuration domain name
	 * @return string
	 */
	static function load($dimension, $nodeId, $domain);
}
