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

namespace Foomo\Site\App;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class Model
{
	// --------------------------------------------------------------------------------------------
	// ~ Constants
	// --------------------------------------------------------------------------------------------

	const APP_NAME = 'default';

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $id;

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		static $instanceCounter;
		if (is_null($instanceCounter)) {
			$instanceCounter = 0;
		}
		$this->id = static::getClassName() . '-' . $instanceCounter++;
	}


	public static function getClassName()
	{
		return 'foomo-app-' . static::APP_NAME;
	}

	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * Application data
	 *
	 * @var mixed
	 */
	public $data;
}
