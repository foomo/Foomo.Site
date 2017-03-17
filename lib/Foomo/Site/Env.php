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

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  franklin
 */
class Env implements EnvInterface
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * @var string[]
	 */
	private $groups = [];

	// --------------------------------------------------------------------------------------------
	// ~ Constructor
	// --------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
	}

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 */
	public static function boot()
	{
		static::getInstance();
//		static::updateLocaleChain();
	}

	/**
	 * @inheritdoc
	 *
	 * @return string[]
	 */
	public static function getGroups()
	{
		return static::getInstance()->groups;
	}

	/**
	 * @param string[] $groups
	 */
	public static function setGroups($groups)
	{
		if (count(array_diff($groups, static::getGroups())) > 0) {
			$config = Module::getSiteConfig();
			if ($config->areValidGroups($groups)) {
				static::getInstance(true)->groups = array_unique($groups);
			} else {
				trigger_error("Invalid groups: " . join(', ', $groups), E_USER_WARNING);
			}
		}
	}

	// --------------------------------------------------------------------------------------------
	// ~ Private static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @param bool $write return env in write mode
	 * @return static
	 */
	protected static function getInstance($write = false)
	{
		if(\Foomo\Session::getEnabled()) {
			if ($write) {
				\Foomo\Session::lockAndLoad();
			}
			return \Foomo\Session::getClassInstance(get_called_class());
		} else {
			$className = get_called_class();
			return new $className;
		}

	}
}
