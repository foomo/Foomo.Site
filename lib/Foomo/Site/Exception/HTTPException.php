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

namespace Foomo\Site\Exception;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class HTTPException extends \Exception
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const MSG_CONTENT_SERVER_UNAVAILABLE = 'CONTENT_SERVER_UNAVAILABLE';
	const MSG_CONTENT_SERVER_NAVIGATION_MISSING = 'CONTENT_SERVER_NAVIGATION_MISSING';

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 * @param int        $code HTTP status code, such as 404, 500, etc.
	 * @param string     $message
	 * @param \Exception $previous
	 */
	public function __construct($code, $message = '', $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * Set the HTTP response code
	 *
	 * @return $this
	 */
	public function setResponseCode()
	{
		http_response_code($this->getCode());
		return $this;
	}
}
