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

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
	// --------------------------------------------------------------------------------------------
	// ~ Public methods
	// --------------------------------------------------------------------------------------------

	/**
	 * @todo: why can't I run these tests when destroying?
	 */
	public function setUp()
	{
		if (\Foomo\Session::getEnabled()) {
			#\Foomo\Session::destroy();
		} else {
			$this->markTestSkipped('session must be enabled / configured');
		}
	}

	/**
	 *
	 */
	public function testGetLanguage()
	{
		$session = Site::getSession();
		$config = Module::getSiteConfig();

		$this->assertEquals(
			$config->getDefaultLanguage(),
			$session::getLanguage(),
			"default language does not match"
		);
	}

	/**
	 *
	 */
	public function testGetRegion()
	{
		$session = Site::getSession();
		$config = Module::getSiteConfig();

		$this->assertEquals(
			$config->getDefaultRegion(),
			$session::getRegion(),
			"default region does not match"
		);
	}

	/**
	 *
	 */
	public function testGetLocale()
	{
		$session = Site::getSession();
		$config = Module::getSiteConfig();

		$this->assertEquals(
			$config->getDefaultLocale(),
			$session::getLocale(),
			"default locale does not match"
		);
	}
}
